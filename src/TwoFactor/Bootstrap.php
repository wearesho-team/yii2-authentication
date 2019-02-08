<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use yii\base;

/**
 * Class Bootstrap
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class Bootstrap extends base\BaseObject implements base\BootstrapInterface
{
    /** @var string|array|ConfigInterface */
    public $config = EnvironmentConfig::class;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        \Yii::$container->set(ConfigInterface::class, $this->config);
    }
}
