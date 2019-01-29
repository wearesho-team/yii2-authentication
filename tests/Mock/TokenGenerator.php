<?php

namespace Wearesho\Yii2\Authentication\Tests\Mock;

use Wearesho\Yii2\Authentication\TwoFactor;

/**
 * Class TokenGenerator
 * @package Wearesho\Yii2\Authentication\Tests\Mock
 */
class TokenGenerator implements TwoFactor\TokenGeneratorInterface
{
    public function generate(): string
    {
        return '';
    }
}
