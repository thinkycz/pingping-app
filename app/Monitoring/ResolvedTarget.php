<?php

namespace App\Monitoring;

readonly class ResolvedTarget
{
    public function __construct(
        public string $url,
        public string $scheme,
        public string $host,
        public int $port,
        public string $ip,
    ) {}

    public function curlResolveEntry(): string
    {
        $address = str_contains($this->ip, ':') ? "[{$this->ip}]" : $this->ip;

        return "{$this->host}:{$this->port}:{$address}";
    }

    public function socketAddress(): string
    {
        $address = str_contains($this->ip, ':') ? "[{$this->ip}]" : $this->ip;

        return "{$address}:{$this->port}";
    }
}
