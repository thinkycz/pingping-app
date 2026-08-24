<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_page_returns_not_found(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Error')
                ->where('status', 404));
    }

    public function test_monitor_owned_by_another_user_returns_forbidden(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->actingAs(User::factory()->create())
            ->get(route('monitors.show', Monitor::factory()->create()))
            ->assertForbidden()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Error')
                ->where('status', 403));
    }
}
