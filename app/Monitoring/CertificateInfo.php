<?php

namespace App\Monitoring;

use Carbon\CarbonInterface;

readonly class CertificateInfo
{
    public function __construct(
        public string $status,
        public ?CarbonInterface $expiresAt,
    ) {}
}
