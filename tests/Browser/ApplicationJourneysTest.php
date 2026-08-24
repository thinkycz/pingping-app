<?php

namespace Tests\Browser;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ApplicationJourneysTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_public_authentication_verification_and_language_journey(): void
    {
        $this->browse(function (Browser $browser): void {
            $this->setViewport($browser, 390, 844);

            $browser->visit('/')
                ->assertSee('Know when your website needs attention.')
                ->assertVisible('@language-cs')
                ->assertVisible('@landing-login');
            $this->captureVerificationScreenshot($browser, 'landing-390-en');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->click('@language-cs')
                ->waitForText('Zjistěte, kdy váš web potřebuje pozornost.');
            $this->captureVerificationScreenshot($browser, 'landing-390-cs');

            $browser->click('@language-en')
                ->waitForText('Know when your website needs attention.')
                ->visit('/register')
                ->assertSee('Create your account');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->type('#name', 'Browser User')
                ->type('#email', 'browser@example.com')
                ->type('#password', 'secure-password')
                ->type('#password_confirmation', 'secure-password')
                ->press('Create account')
                ->waitForLocation('/verify-email')
                ->assertSee('Check your inbox')
                ->press('Resend verification email')
                ->waitForText('A new verification link has been sent.')
                ->press('Log out')
                ->waitForLocation('/')
                ->visit('/forgot-password')
                ->assertSee('Reset your password');
            $this->assertNoSeriousAccessibilityViolations($browser);
        });
    }

    public function test_dashboard_search_filter_pagination_and_monitor_crud_journey(): void
    {
        $user = User::factory()->create();
        Monitor::factory()->create([
            'user_id' => $user->id,
            'alias' => 'Needle site',
            'url' => 'https://needle.example.com',
        ]);
        Monitor::factory()->create([
            'user_id' => $user->id,
            'alias' => 'Pending site',
            'last_checked_at' => null,
        ]);
        Monitor::factory()->count(10)->create(['user_id' => $user->id]);
        Monitor::factory()->create([
            'user_id' => $user->id,
            'alias' => 'Unavailable site',
            'status' => 'Down',
            'uptime_30d' => 96.4,
            'failure_code' => 'timeout',
        ]);
        Monitor::factory()->create([
            'user_id' => $user->id,
            'alias' => 'Paused site',
            'is_active' => false,
        ]);

        $this->browse(function (Browser $browser) use ($user): void {
            $this->setViewport($browser, 1440, 1000);

            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('Your monitors')
                ->assertVisible('@monitor-search');
            $this->captureVerificationScreenshot($browser, 'dashboard-1440');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $this->setViewport($browser, 390, 844);
            $browser->visit('/dashboard')
                ->assertVisible('@mobile-monitor-list');
            $this->captureVerificationScreenshot($browser, 'dashboard-390');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->scrollIntoView('@pagination-next')
                ->assertVisible('@pagination-next');
            $this->captureVerificationScreenshot($browser, 'dashboard-pagination-390');

            $this->setViewport($browser, 1440, 1000);
            $browser->visit('/dashboard');

            $browser->type('@monitor-search', 'Needle')
                ->pause(700)
                ->waitForText('Needle site')
                ->assertDontSee('Pending site')
                ->visit('/dashboard')
                ->click('@filter-pending')
                ->waitForText('Pending site')
                ->click('@filter-all')
                ->pause(500)
                ->clickLink('Next')
                ->waitUntilMissing('.fixed[role="status"]', 2)
                ->assertQueryStringHas('page', '2')
                ->click('@new-monitor')
                ->waitForLocation('/monitors/create');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->type('@monitor-url', 'http://127.0.0.1/private')
                ->type('@monitor-alias', 'Unsafe')
                ->click('@create-monitor')
                ->waitForText('Enter a public HTTP or HTTPS URL')
                ->clear('@monitor-url')
                ->clear('@monitor-alias')
                ->type('@monitor-url', 'https://example.com')
                ->type('@monitor-alias', 'Example status')
                ->click('@create-monitor')
                ->waitForText('Pending')
                ->assertSee('Example status');
            $this->captureVerificationScreenshot($browser, 'monitor-pending-1440');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->clear('@settings-alias')
                ->type('@settings-alias', 'Updated example')
                ->click('@save-monitor')
                ->waitForText('Monitor settings saved.')
                ->script('window.scrollTo({ top: 0, behavior: "instant" })');
            $browser->pause(200)
                ->click('@toggle-monitor')
                ->waitForText('Paused');
            $this->captureVerificationScreenshot($browser, 'monitor-paused-1440');

            $browser->click('@delete-monitor')
                ->waitForText('Delete this monitor?');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->click('@confirm-delete')
                ->waitForLocation('/dashboard')
                ->assertSee('Monitor deleted.');
        });
    }

    public function test_profile_password_and_account_deletion_journey(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->browse(function (Browser $browser) use ($user): void {
            $this->setViewport($browser, 768, 900);

            $browser->loginAs($user)
                ->visit('/profile')
                ->assertSee('Account settings');
            $this->captureVerificationScreenshot($browser, 'profile-768');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->clear('#name')
                ->type('#name', 'Updated Browser User')
                ->press('Save changes')
                ->waitForText('Saved')
                ->type('#current_password', 'password')
                ->type('#new_password', 'new-secure-password')
                ->type('#password_confirmation', 'new-secure-password')
                ->press('Update password')
                ->waitForText('Saved')
                ->click('@delete-account')
                ->waitForText('Delete your PingPing account?');
            $this->assertNoSeriousAccessibilityViolations($browser);

            $browser->type('#delete_password', 'new-secure-password')
                ->click('@confirm-account-delete')
                ->waitForLocation('/')
                ->assertSee('Know when your website needs attention.');
        });
    }
}
