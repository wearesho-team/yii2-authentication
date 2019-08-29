<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii2\Token;

/**
 * Class TokenEntity
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class TokenEntity extends Token\Entity
{
    public function __construct(
        ?string $ip,
        string $login,
        string $value,
        \DateInterval $ttl
    ) {
        parent::__construct(
            'login',
            static::generateOwner($ip, $login),
            $value,
            (new \DateTime)->add($ttl)
        );
    }

    public static function generateOwner(?string $ip, string $login): string
    {
        return hash('sha256', $ip . hash('sha256', $login));
    }
}
