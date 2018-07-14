<?php

namespace BotMan\Drivers\Line\Providers;

use BotMan\Drivers\Line\LineDriver;
use Illuminate\Support\ServiceProvider;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Line\LineAudioDriver;
use BotMan\Drivers\Line\LineImageDriver;
use BotMan\Drivers\Line\LineVideoDriver;
use BotMan\Drivers\Line\LineLocationDriver;
use BotMan\Studio\Providers\StudioServiceProvider;

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
                __DIR__.'/../../stubs/line.php' => config_path('botman/line.php'),
            ]);
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../stubs/line.php', 'botman.line');
    }

    /**
     * Load BotMan drivers.
     */
    protected function loadDrivers()
    {
        DriverManager::loadDriver(LineDriver::class);
        DriverManager::loadDriver(LineAudioDriver::class);
        DriverManager::loadDriver(LineImageDriver::class);
        DriverManager::loadDriver(LineLocationDriver::class);
        DriverManager::loadDriver(LineVideoDriver::class);
    }

    /**
     * @return bool
     */
    protected function isRunningInBotManStudio()
    {
        return class_exists(StudioServiceProvider::class);
    }
}
