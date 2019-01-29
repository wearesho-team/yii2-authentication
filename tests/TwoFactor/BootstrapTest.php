<?php

namespace Wearesho\Yii2\Authentication\Tests\TwoFactor;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii2\Authentication\TwoFactor;
use yii\web;

/**
 * Class BootstrapTest
 * @package Wearesho\Yii2\Authentication\Tests\TwoFactor
 */
class BootstrapTest extends TestCase
{
    public function testBootstrap(): void
    {
        $app = new web\Application([
            'id' => 'test',
            'basePath' => __DIR__,
        ]);

        $this->assertFalse(\Yii::$container->has(TwoFactor\ConfigInterface::class));

        $bootstrap = new TwoFactor\Bootstrap([
            'config' => TwoFactor\EnvironmentConfig::class,
        ]);
        $bootstrap->bootstrap($app);
        $this->assertInstanceOf(
            TwoFactor\EnvironmentConfig::class,
            \Yii::$container->get(TwoFactor\ConfigInterface::class)
        );
    }
}
