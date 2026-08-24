<?php

namespace App\Http\Requests;

use App\Models\Monitor;
use App\Monitoring\PublicTargetResolver;
use App\Rules\PublicMonitorUrl;
use Illuminate\Foundation\Http\FormRequest;

class MonitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $monitor = $this->route('monitor');

        return ! $monitor instanceof Monitor || $this->user()->can('update', $monitor);
    }

    public function rules(PublicTargetResolver $targets): array
    {
        return [
            'url' => ['required', 'string', 'max:255', new PublicMonitorUrl($targets)],
            'alias' => ['nullable', 'string', 'max:255'],
            'interval' => ['required', 'integer', 'in:5,15,30,60'],
        ];
    }
}
