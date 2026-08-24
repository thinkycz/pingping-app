<?php

namespace App\Monitoring;

use App\Monitoring\Contracts\TlsInspector;
use App\Monitoring\Exceptions\UnsafeTargetException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class HttpMonitorProbe
{
    private const MAX_REDIRECTS = 5;

    public function __construct(
        private readonly PublicTargetResolver $targets,
        private readonly TlsInspector $tls,
    ) {}

    public function run(string $url): ProbeResult
    {
        $startedAt = microtime(true);
        $redirects = 0;

        try {
            while (true) {
                $target = $this->targets->resolve($url);
                $response = $this->requestWithRetry($target);
                $status = $response->status();

                if ($this->isRedirect($status) && $response->header('Location')) {
                    if ($redirects >= self::MAX_REDIRECTS) {
                        $this->close($response);

                        return ProbeResult::failure(
                            $this->elapsedMilliseconds($startedAt),
                            'too_many_redirects',
                            'The target exceeded five redirects.',
                            $status,
                        );
                    }

                    $url = (string) UriResolver::resolve(
                        new Uri($url),
                        new Uri($response->header('Location')),
                    );
                    $redirects++;
                    $this->close($response);

                    continue;
                }

                try {
                    $certificate = $this->certificateFor($target);
                } finally {
                    $this->close($response);
                }

                if ($status >= 200 && $status < 400) {
                    return new ProbeResult(
                        'Up',
                        $this->elapsedMilliseconds($startedAt),
                        $status,
                        $certificate->status,
                        $certificate->expiresAt,
                    );
                }

                return ProbeResult::failure(
                    $this->elapsedMilliseconds($startedAt),
                    'http_error',
                    "HTTP {$status}",
                    $status,
                    $certificate->status,
                    $certificate->expiresAt,
                );
            }
        } catch (UnsafeTargetException $exception) {
            return ProbeResult::failure(
                $this->elapsedMilliseconds($startedAt),
                $exception->failureCode,
                'The target is not an approved public HTTP or HTTPS address.',
            );
        } catch (ConnectionException $exception) {
            $isTimeout = str_contains(strtolower($exception->getMessage()), 'timed out');

            return ProbeResult::failure(
                $this->elapsedMilliseconds($startedAt),
                $isTimeout ? 'timeout' : 'connection_failed',
                $isTimeout ? 'The request timed out.' : 'The target could not be reached.',
            );
        } catch (Throwable) {
            return ProbeResult::failure(
                $this->elapsedMilliseconds($startedAt),
                'tls_failed',
                'TLS trust or hostname verification failed.',
                sslStatus: 'Invalid',
            );
        }
    }

    private function requestWithRetry(ResolvedTarget $target): Response
    {
        $attempt = 0;

        do {
            $attempt++;

            try {
                $response = $this->request($target);
            } catch (ConnectionException $exception) {
                if ($attempt < 2) {
                    continue;
                }

                throw $exception;
            }

            if ($response->serverError() && $attempt < 2) {
                $this->close($response);

                continue;
            }

            return $response;
        } while ($attempt < 2);

        throw new ConnectionException('The target could not be reached.');
    }

    private function request(ResolvedTarget $target): Response
    {
        $options = [
            'allow_redirects' => false,
            'curl' => [CURLOPT_RESOLVE => [$target->curlResolveEntry()]],
            'proxy' => '',
            'stream' => true,
            'verify' => true,
        ];

        return Http::accept('*/*')
            ->withUserAgent('PingPing Monitor/1.0')
            ->connectTimeout(5)
            ->timeout(10)
            ->withOptions($options)
            ->get($target->url);
    }

    private function certificateFor(ResolvedTarget $target): CertificateInfo
    {
        if ($target->scheme !== 'https') {
            return new CertificateInfo('None', null);
        }

        return $this->tls->inspect($target);
    }

    private function isRedirect(int $status): bool
    {
        return in_array($status, [301, 302, 303, 307, 308], true);
    }

    private function elapsedMilliseconds(float $startedAt): int
    {
        return max(0, (int) round((microtime(true) - $startedAt) * 1000));
    }

    private function close(Response $response): void
    {
        $response->toPsrResponse()->getBody()->close();
    }
}
