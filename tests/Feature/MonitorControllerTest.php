<?php

namespace Tests\Feature;

use App\Jobs\PingMonitorJob;
use App\Models\Monitor;
use App\Models\User;
use App\Monitoring\Contracts\DnsResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MonitorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(DnsResolver::class, new class implements DnsResolver
        {
            public function resolve(string $host): array
            {
                return ['93.184.216.34'];
            }
        });
    }

    public function test_unverified_user_is_redirected_from_monitoring_routes(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_dashboard_returns_global_counts_and_applies_status_filter(): void
    {
        $user = User::factory()->create();
        Monitor::factory()->create(['user_id' => $user->id, 'status' => 'Up']);
        Monitor::factory()->create(['user_id' => $user->id, 'status' => 'Down']);
        Monitor::factory()->create(['user_id' => $user->id, 'is_active' => false]);
        Monitor::factory()->create(['user_id' => $user->id, 'last_checked_at' => null]);
        Monitor::factory()->create();

        $this->actingAs($user)->get('/dashboard?status=pending')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('stats.total', 4)
                ->where('stats.up', 1)
                ->where('stats.down', 1)
                ->where('stats.paused', 1)
                ->where('stats.pending', 1)
                ->where('filters.status', 'pending')
                ->has('monitors.data', 1)
                ->where('monitors.data.0.display_state', 'pending')
                ->missing('monitors.data.0.user_id'));
    }

    public function test_dashboard_searches_alias_and_url(): void
    {
        $user = User::factory()->create();
        Monitor::factory()->create(['user_id' => $user->id, 'alias' => 'Storefront', 'url' => 'https://shop.example.com']);
        Monitor::factory()->create(['user_id' => $user->id, 'alias' => 'Docs', 'url' => 'https://manual.example.com']);

        $this->actingAs($user)->get('/dashboard?search=store')
            ->assertInertia(fn (Assert $page) => $page
                ->has('monitors.data', 1)
                ->where('monitors.data.0.alias', 'Storefront'));
    }

    public function test_user_can_create_a_pending_monitor_and_first_check_is_dispatched(): void
    {
        Queue::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/monitors', [
            'url' => 'https://example.com',
            'alias' => 'Example',
            'interval' => 5,
        ]);

        $monitor = Monitor::query()->sole();
        $response->assertRedirect(route('monitors.show', $monitor));
        $response->assertSessionHas('success');
        $this->assertNull($monitor->last_checked_at);
        $this->assertSame('pending', $monitor->displayState());
        Queue::assertPushed(PingMonitorJob::class, fn (PingMonitorJob $job): bool => $job->monitorId === $monitor->id);
    }

    public function test_private_target_is_rejected(): void
    {
        Queue::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->post('/monitors', [
            'url' => 'http://127.0.0.1/admin',
            'alias' => 'Unsafe',
            'interval' => 5,
        ])->assertSessionHasErrors('url');

        $this->assertDatabaseCount('monitors', 0);
        Queue::assertNothingPushed();
    }

    public function test_user_cannot_access_or_mutate_another_users_monitor(): void
    {
        $user = User::factory()->create();
        $monitor = Monitor::factory()->create();

        $this->actingAs($user)->get(route('monitors.show', $monitor))->assertForbidden();
        $this->actingAs($user)->put(route('monitors.update', $monitor), [
            'url' => 'https://example.com',
            'alias' => 'Changed',
            'interval' => 5,
        ])->assertForbidden();
        $this->actingAs($user)->patch(route('monitors.toggle', $monitor))->assertForbidden();
        $this->actingAs($user)->delete(route('monitors.destroy', $monitor))->assertForbidden();
    }

    public function test_user_can_pause_resume_update_and_delete_their_monitor(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $monitor = Monitor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->patch(route('monitors.toggle', $monitor))
            ->assertSessionHas('success');
        $this->assertFalse($monitor->fresh()->is_active);

        $this->actingAs($user)->patch(route('monitors.toggle', $monitor));
        $this->assertTrue($monitor->fresh()->is_active);
        Queue::assertPushed(PingMonitorJob::class);

        $this->actingAs($user)->put(route('monitors.update', $monitor), [
            'url' => 'https://example.com',
            'alias' => 'Updated',
            'interval' => 15,
        ])->assertSessionHas('success');
        $this->assertDatabaseHas('monitors', ['id' => $monitor->id, 'alias' => 'Updated', 'interval' => 15]);

        $this->actingAs($user)->delete(route('monitors.destroy', $monitor))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('monitors', ['id' => $monitor->id]);
    }
}
