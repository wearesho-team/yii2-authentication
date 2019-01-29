<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

/**
 * Interface TokenGeneratorInterface
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
interface TokenGeneratorInterface
{
    public function generate(): string;
}
