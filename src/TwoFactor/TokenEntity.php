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
    /** @var string|null */
    protected $ip;

    /** @var string */
    protected $login;

    public function __construct(
        ?string $ip,
        string $login,
        string $value,
        \DateInterval $ttl
    ) {
        $this->ip = $ip;
        $this->login = $login;

        parent::__construct(
            'login',
            '',
            $value,
            date_create()->add($ttl)
        );
    }

    public function getIp():?string
    {
        return $this->ip;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getOwner(): string
    {
        return static::generateOwner($this->ip, $this->login);
    }

    public static function generateOwner(?string $ip, string $login): string
    {
        return hash('sha256', $ip . hash('sha256', $login));
    }
}
