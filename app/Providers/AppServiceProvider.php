<?php

namespace App\Providers;

use App\Monitoring\Contracts\DnsResolver;
use App\Monitoring\Contracts\TlsInspector;
use App\Monitoring\OpenSslTlsInspector;
use App\Monitoring\SystemDnsResolver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DnsResolver::class, SystemDnsResolver::class);
        $this->app->bind(TlsInspector::class, OpenSslTlsInspector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
