<?php

namespace App\Rules;

use App\Monitoring\Exceptions\UnsafeTargetException;
use App\Monitoring\PublicTargetResolver;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PublicMonitorUrl implements ValidationRule
{
    public function __construct(private readonly PublicTargetResolver $targets) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $this->targets->resolve((string) $value);
        } catch (UnsafeTargetException) {
            $fail(__('validation.public_monitor_url'));
        }
    }
}
