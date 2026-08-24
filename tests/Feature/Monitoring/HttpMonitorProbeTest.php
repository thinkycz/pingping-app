<?php

namespace Tests\Feature\Monitoring;

use App\Monitoring\CertificateInfo;
use App\Monitoring\Contracts\DnsResolver;
use App\Monitoring\Contracts\TlsInspector;
use App\Monitoring\HttpMonitorProbe;
use App\Monitoring\PublicTargetResolver;
use App\Monitoring\ResolvedTarget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HttpMonitorProbeTest extends TestCase
{
    public function test_it_revalidates_redirects_and_returns_the_final_success(): void
    {
        Http::fake([
            'https://example.com/start' => Http::response('', 302, ['Location' => 'https://www.example.com/health']),
            'https://www.example.com/health' => Http::response('OK', 204),
        ]);

        $dns = new RecordingDnsResolver([
            'example.com' => ['93.184.216.34'],
            'www.example.com' => ['93.184.216.35'],
        ]);
        $probe = new HttpMonitorProbe(new PublicTargetResolver($dns), new ValidTlsInspector);

        $result = $probe->run('https://example.com/start');

        $this->assertSame('Up', $result->status);
        $this->assertSame(204, $result->httpStatus);
        $this->assertSame(['example.com', 'www.example.com'], $dns->resolvedHosts);
        Http::assertSentCount(2);
    }

    public function test_it_retries_one_server_error_then_succeeds(): void
    {
        Http::fakeSequence()
            ->push('Unavailable', 503)
            ->push('OK', 200);

        $probe = $this->probe();

        $result = $probe->run('https://example.com');

        $this->assertSame('Up', $result->status);
        $this->assertSame(200, $result->httpStatus);
        Http::assertSentCount(2);
    }

    public function test_it_retries_one_connection_failure_then_succeeds(): void
    {
        Http::fakeSequence()
            ->pushFailedConnection('cURL error 28: Operation timed out')
            ->push('OK', 200);

        $result = $this->probe()->run('https://example.com');

        $this->assertSame('Up', $result->status);
        Http::assertSentCount(2);
    }

    public function test_final_timeout_is_down_after_one_retry(): void
    {
        Http::fakeSequence()
            ->pushFailedConnection('cURL error 28: Operation timed out')
            ->pushFailedConnection('cURL error 28: Operation timed out');

        $result = $this->probe()->run('https://example.com');

        $this->assertSame('Down', $result->status);
        $this->assertSame('timeout', $result->failureCode);
        Http::assertSentCount(2);
    }

    public function test_final_server_error_is_down_after_one_retry(): void
    {
        Http::fakeSequence()
            ->push('Unavailable', 503)
            ->push('Unavailable', 503);

        $result = $this->probe()->run('https://example.com');

        $this->assertSame('Down', $result->status);
        $this->assertSame(503, $result->httpStatus);
        $this->assertSame('http_error', $result->failureCode);
        Http::assertSentCount(2);
    }

    public function test_it_does_not_retry_a_client_error(): void
    {
        Http::fake(['https://example.com' => Http::response('Not found', 404)]);

        $result = $this->probe()->run('https://example.com');

        $this->assertSame('Down', $result->status);
        $this->assertSame(404, $result->httpStatus);
        $this->assertSame('http_error', $result->failureCode);
        Http::assertSentCount(1);
    }

    public function test_terminal_redirect_response_without_location_is_up(): void
    {
        Http::fake(['https://example.com' => Http::response('', 304)]);

        $result = $this->probe()->run('https://example.com');

        $this->assertSame('Up', $result->status);
        $this->assertSame(304, $result->httpStatus);
        Http::assertSentCount(1);
    }

    public function test_it_stops_after_five_redirects(): void
    {
        Http::fake(fn ($request) => Http::response('', 302, ['Location' => $request->url().'/next']));

        $result = $this->probe()->run('https://example.com');

        $this->assertSame('Down', $result->status);
        $this->assertSame('too_many_redirects', $result->failureCode);
        Http::assertSentCount(6);
    }

    public function test_it_rejects_a_redirect_to_a_private_target_before_requesting_it(): void
    {
        Http::fake([
            'https://example.com' => Http::response('', 302, ['Location' => 'http://127.0.0.1/secrets']),
        ]);

        $result = $this->probe()->run('https://example.com');

        $this->assertSame('Down', $result->status);
        $this->assertSame('non_public_address', $result->failureCode);
        Http::assertSentCount(1);
    }

    public function test_tls_verification_failure_marks_https_down(): void
    {
        Http::fake(['https://example.com' => Http::response('OK', 200)]);
        $probe = new HttpMonitorProbe(
            new PublicTargetResolver(new RecordingDnsResolver(['example.com' => ['93.184.216.34']])),
            new FailingTlsInspector,
        );

        $result = $probe->run('https://example.com');

        $this->assertSame('Down', $result->status);
        $this->assertSame('tls_failed', $result->failureCode);
        $this->assertSame('Invalid', $result->sslStatus);
    }

    private function probe(): HttpMonitorProbe
    {
        return new HttpMonitorProbe(
            new PublicTargetResolver(new RecordingDnsResolver([
                'example.com' => ['93.184.216.34'],
            ])),
            new ValidTlsInspector,
        );
    }
}

class RecordingDnsResolver implements DnsResolver
{
    public array $resolvedHosts = [];

    public function __construct(private readonly array $records) {}

    public function resolve(string $host): array
    {
        $this->resolvedHosts[] = $host;

        return $this->records[$host] ?? [];
    }
}

class ValidTlsInspector implements TlsInspector
{
    public function inspect(ResolvedTarget $target): CertificateInfo
    {
        return new CertificateInfo('Valid', Carbon::now()->addMonth());
    }
}

class FailingTlsInspector implements TlsInspector
{
    public function inspect(ResolvedTarget $target): CertificateInfo
    {
        throw new \RuntimeException('Untrusted certificate detail that must not be stored.');
    }
}
