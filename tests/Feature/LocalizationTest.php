<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\User;
use App\Notifications\MonitorStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_language_choice_is_stored_in_the_session(): void
    {
        $this->post(route('language.store'), ['language' => 'cs'])
            ->assertSessionHas('locale', 'cs');
    }

    public function test_authenticated_language_choice_is_persisted_for_queued_mail(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)->post(route('language.store'), ['language' => 'cs']);

        $this->assertSame('cs', $user->fresh()->locale);
        $this->assertSame('cs', $user->preferredLocale());
    }

    public function test_status_email_uses_the_users_czech_locale(): void
    {
        $user = User::factory()->create(['locale' => 'cs']);
        $monitor = Monitor::factory()->create(['user_id' => $user->id, 'status' => 'Down']);
        app()->setLocale($user->preferredLocale());

        $mail = (new MonitorStatusChanged($monitor, 'Down'))->toMail($user);

        $this->assertStringContainsString('je nefunkční', $mail->subject);
        $this->assertSame('Zobrazit monitor', $mail->actionText);
    }
}
