<?php

namespace App\Monitoring;

use Carbon\CarbonInterface;

readonly class ProbeResult
{
    public function __construct(
        public string $status,
        public int $responseTimeMs,
        public ?int $httpStatus,
        public string $sslStatus = 'None',
        public ?CarbonInterface $sslExpirationDate = null,
        public ?string $failureCode = null,
        public ?string $failureDetail = null,
    ) {}

    public static function failure(
        int $responseTimeMs,
        string $failureCode,
        string $failureDetail,
        ?int $httpStatus = null,
        string $sslStatus = 'None',
        ?CarbonInterface $sslExpirationDate = null,
    ): self {
        return new self(
            'Down',
            $responseTimeMs,
            $httpStatus,
            $sslStatus,
            $sslExpirationDate,
            $failureCode,
            $failureDetail,
        );
    }
}
