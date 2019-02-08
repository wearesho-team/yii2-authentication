<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

/**
 * Interface ConfigInterface
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
interface ConfigInterface
{
    /**
     * How many seconds token will be available
     * @return int
     */
    public function getTokenLifetime(): int;
}
