<?php

namespace App\Monitoring\Contracts;

use App\Monitoring\CertificateInfo;
use App\Monitoring\ResolvedTarget;

interface TlsInspector
{
    public function inspect(ResolvedTarget $target): CertificateInfo;
}
