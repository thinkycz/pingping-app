<?php

namespace Tests\Unit\Monitoring;

use App\Monitoring\Contracts\DnsResolver;
use App\Monitoring\Exceptions\UnsafeTargetException;
use App\Monitoring\PublicTargetResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PublicTargetResolverTest extends TestCase
{
    public function test_it_resolves_and_pins_a_public_https_target(): void
    {
        $resolver = new PublicTargetResolver(new FakeDnsResolver([
            'example.com' => ['93.184.216.34'],
        ]));

        $target = $resolver->resolve('https://example.com/status?full=1');

        $this->assertSame('https://example.com/status?full=1', $target->url);
        $this->assertSame('example.com', $target->host);
        $this->assertSame(443, $target->port);
        $this->assertSame('93.184.216.34', $target->ip);
        $this->assertSame('example.com:443:93.184.216.34', $target->curlResolveEntry());
    }

    #[DataProvider('unsafeTargets')]
    public function test_it_rejects_unsafe_targets(string $url, array $addresses = []): void
    {
        $resolver = new PublicTargetResolver(new FakeDnsResolver([
            'example.com' => $addresses,
            'internal.test' => $addresses,
        ]));

        $this->expectException(UnsafeTargetException::class);

        $resolver->resolve($url);
    }

    public static function unsafeTargets(): array
    {
        return [
            'unsupported scheme' => ['ftp://example.com/file', ['93.184.216.34']],
            'credentials' => ['https://admin:secret@example.com', ['93.184.216.34']],
            'unsupported port' => ['https://example.com:8443', ['93.184.216.34']],
            'localhost name' => ['http://localhost', ['127.0.0.1']],
            'loopback ipv4' => ['http://127.0.0.1'],
            'private ipv4' => ['http://10.0.0.1'],
            'link-local ipv4' => ['http://169.254.10.2'],
            'multicast ipv4' => ['http://224.0.0.1'],
            'loopback ipv6' => ['http://[::1]'],
            'ipv4-mapped loopback' => ['http://[::ffff:127.0.0.1]'],
            'private ipv6' => ['http://[fd00::1]'],
            'link-local ipv6' => ['http://[fe80::1]'],
            'multicast ipv6' => ['http://[ff02::1]'],
            'unresolved host' => ['https://example.com', []],
            'mixed public and private dns' => ['https://internal.test', ['93.184.216.34', '10.0.0.5']],
        ];
    }
}

class FakeDnsResolver implements DnsResolver
{
    public function __construct(private readonly array $records) {}

    public function resolve(string $host): array
    {
        return $this->records[$host] ?? [];
    }
}
