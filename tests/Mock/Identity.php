<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\Tests\Mock;

use Wearesho\Yii2\Authentication;

/**
 * Class Identity
 * @package Wearesho\Yii2\Authentication\Tests\Mock
 * @internal
 */
class Identity implements Authentication\IdentityInterface
{
    /** @var Identity|null */
    public static $willFind = null;

    public function getId(): int
    {
        return 1;
    }

    public function validatePassword(string $password): bool
    {
        return true;
    }

    public static function findIdentityByLogin(string $login): ?Authentication\IdentityInterface
    {
        return static::$willFind;
    }
}
