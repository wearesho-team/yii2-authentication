<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii2\Token;

class TokenEntity extends Token\Entity
{
    private string $login;

    public function __construct(
        string $ip,
        string $login,
        string $value,
        \DateInterval $ttl
    ) {
        $this->login = $login;
        parent::__construct(
            'login',
            static::generateOwner($ip, $login),
            $value,
            (new \DateTime())->add($ttl)
        );
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public static function generateOwner(string $ip, string $login): string
    {
        return hash('sha256', $ip . hash('sha256', $login));
    }
}
