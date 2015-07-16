<?php

/*
 * This file is part of Alt Three Sentry.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AltThree\Tests\Sentry;

use AltThree\Sentry\SentryServiceProvider;
use AltThree\Sentry\Logger;
use Raven_Client as Sentry;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author James Brooks <james@alt-three.com>
 */
class ServiceProviderTest extends AbstractPackageTestCase
{
    use ServiceProviderTrait;

    protected function getServiceProviderClass($app)
    {
        return SentryServiceProvider::class;
    }

    public function testRepositoryFactoryIsInjectable()
    {
        $this->app->config->set('sentry.key', 'qwertyuiop');

        $this->assertIsInjectable(Sentry::class);
    }

    public function testLoggerIsInjectable()
    {
        $this->app->config->set('sentry.key', 'qwertyuiop');

        $this->assertIsInjectable(Logger::class);
    }
}
