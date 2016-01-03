<?php

/*
 * This file is part of Alt Three Sentry.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AltThree\Sentry;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Raven_Client as Sentry;

/**
 * This is the sentry service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author James Brooks <james@alt-three.com>
 */
class SentryServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig($this->app);
    }

    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function setupConfig(Application $app)
    {
        $source = realpath(__DIR__.'/../config/sentry.php');

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => config_path('sentry.php')]);
        } elseif ($app instanceof LumenApplication) {
            $app->configure('sentry');
        }

        $this->mergeConfigFrom($source, 'sentry');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSentry();
        $this->registerLogger();
    }

    /**
     * Register the sentry class.
     *
     * @return void
     */
    protected function registerSentry()
    {
        $this->app->singleton('sentry', function (Application $app) {
            return new Sentry($app->config->get('sentry.dsn'));
        });

        $this->app->alias('sentry', Sentry::class);
    }

    /**
     * Register the logger class.
     *
     * @return void
     */
    protected function registerLogger()
    {
        $this->app->singleton('sentry.logger', function (Application $app) {
            $sentry = $app['sentry'];
            $user = function () use ($app) {
                if ($user = $app->auth->user()) {
                    return $user->toArray();
                }
            };

            return new Logger($sentry, $user);
        });

        $this->app->alias('sentry.logger', Logger::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'sentry', 'sentry.logger',
        ];
    }
}
