<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        $database = dirname(__DIR__).'/database/dusk.sqlite';

        if (! file_exists($database)) {
            touch($database);
        }

        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    protected function assertNoSeriousAccessibilityViolations(\Laravel\Dusk\Browser $browser): void
    {
        $browser->script(file_get_contents(base_path('node_modules/axe-core/axe.min.js')));

        $violations = $browser->driver->executeAsyncScript(<<<'JS'
            const done = arguments[0];
            axe.run(document, { resultTypes: ['violations'] })
                .then((result) => done(result.violations
                    .filter((violation) => ['serious', 'critical'].includes(violation.impact))
                    .map((violation) => `${violation.id}: ${violation.help}\n${violation.nodes
                        .map((node) => `${node.target.join(' ')} — ${node.failureSummary}`)
                        .join('\n')}`)))
                .catch((error) => done([String(error)]));
        JS);

        $this->assertSame([], $violations, implode("\n", $violations));
    }

    protected function setViewport(\Laravel\Dusk\Browser $browser, int $width, int $height): void
    {
        (new ChromeDevToolsDriver($browser->driver))->execute('Emulation.setDeviceMetricsOverride', [
            'width' => $width,
            'height' => $height,
            'deviceScaleFactor' => 1,
            'mobile' => false,
        ]);
    }

    protected function captureVerificationScreenshot(Browser $browser, string $name): void
    {
        $directory = dirname(__DIR__).'/docs/verification/screenshots/2026-08-24';
        $originalDirectory = Browser::$storeScreenshotsAt;

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        try {
            Browser::$storeScreenshotsAt = $directory;
            $browser->screenshot($name);
        } finally {
            Browser::$storeScreenshotsAt = $originalDirectory;
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
