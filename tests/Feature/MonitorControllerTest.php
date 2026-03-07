<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();
        $monitor = Monitor::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        // Assuming inertia testing macros are not explicitly installed, checking for standard ok status is sufficient here,
        // or checking if the monitor url is somewhere in the response.
    }

    public function test_user_can_create_a_monitor(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/monitors', [
            'url' => 'https://example.com',
            'alias' => 'Example',
            'interval' => 5,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('monitors', [
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'alias' => 'Example',
            'interval' => 5,
        ]);
    }

    public function test_user_cannot_view_others_monitor(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $monitor = Monitor::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get("/monitors/{$monitor->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_monitor(): void
    {
        $user = User::factory()->create();
        $monitor = Monitor::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/monitors/{$monitor->id}");

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('monitors', [
            'id' => $monitor->id,
        ]);
    }
}
