<?php

namespace Wearesho\Yii2\Authentication\Tests\TwoFactor;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii2\Authentication\TwoFactor\EnvironmentConfig;

/**
 * Class EnvironmentConfigTest
 * @package Wearesho\Yii2\Authentication\Tests\TwoFactor
 */
class EnvironmentConfigTest extends TestCase
{
    public function testGet(): void
    {
        putenv('TWO_FACTOR_AUTHENTICATION_TOKEN_LIFETIME=30');
        $config = new EnvironmentConfig();
        $this->assertEquals(30, $config->getTokenLifetime());
    }
}
