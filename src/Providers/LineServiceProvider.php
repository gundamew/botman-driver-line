<?php

namespace BotMan\Drivers\Line\Providers;

use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Line\LineDriver;
use BotMan\Studio\Providers\StudioServiceProvider;
use Illuminate\Support\ServiceProvider;

class LineServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->isRunningInBotManStudio()) {
            $this->loadDrivers();

            $this->publishes([
                __DIR__ . '/../../stubs/line.php' => config_path('botman/line.php'),
            ]);

            $this->mergeConfigFrom(__DIR__ . '/../../stubs/line.php', 'botman.line');
        }
    }

    /**
     * Load BotMan drivers.
     */
    protected function loadDrivers()
    {
        DriverManager::loadDriver(LineDriver::class);
    }

    /**
     * @return bool
     */
    protected function isRunningInBotManStudio()
    {
        return class_exists(StudioServiceProvider::class);
    }
}
