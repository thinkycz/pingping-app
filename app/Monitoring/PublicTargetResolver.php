<?php

namespace App\Monitoring;

use App\Monitoring\Contracts\DnsResolver;
use App\Monitoring\Exceptions\UnsafeTargetException;

class PublicTargetResolver
{
    public function __construct(private readonly DnsResolver $dns) {}

    public function resolve(string $url): ResolvedTarget
    {
        $parts = parse_url($url);

        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'])) {
            throw new UnsafeTargetException('invalid_url');
        }

        $scheme = strtolower($parts['scheme']);
        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new UnsafeTargetException('unsupported_scheme');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new UnsafeTargetException('credentials_not_allowed');
        }

        $host = strtolower(rtrim(trim($parts['host'], '[]'), '.'));
        if ($host === '' || $host === 'localhost' || str_ends_with($host, '.localhost')) {
            throw new UnsafeTargetException('local_target');
        }

        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        if (! in_array($port, [80, 443], true)) {
            throw new UnsafeTargetException('unsupported_port');
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : $this->dns->resolve($host);

        if ($addresses === []) {
            throw new UnsafeTargetException('unresolved_host');
        }

        foreach ($addresses as $address) {
            if (! $this->isPublicAddress($address)) {
                throw new UnsafeTargetException('non_public_address');
            }
        }

        return new ResolvedTarget($url, $scheme, $host, $port, $addresses[0]);
    }

    private function isPublicAddress(string $address): bool
    {
        $validated = filter_var(
            $address,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        );

        if ($validated === false) {
            return false;
        }

        $packed = inet_pton($validated);

        if ($packed === false) {
            return false;
        }

        // PHP's reserved-range filter does not reject IPv4 multicast space.
        if (strlen($packed) === 4 && (ord($packed[0]) & 0xF0) === 0xE0) {
            return false;
        }

        // Keep IPv6 multicast targets out even if platform filter behavior differs.
        if (strlen($packed) === 16 && ord($packed[0]) === 0xFF) {
            return false;
        }

        return true;
    }
}
