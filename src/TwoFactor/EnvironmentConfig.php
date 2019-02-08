<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Horat1us\Environment;

/**
 * Class EnvironmentConfig
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class EnvironmentConfig extends Environment\Config implements ConfigInterface
{
    public function __construct(string $keyPrefix = 'TWO_FACTOR_AUTHENTICATION_')
    {
        parent::__construct($keyPrefix);
    }

    public function getTokenLifetime(): int
    {
        return $this->getEnv('TOKEN_LIFETIME');
    }
}
