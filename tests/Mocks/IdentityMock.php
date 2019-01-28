<?php

namespace Wearesho\Yii2\Authentication\Tests\Mocks;

use Wearesho\Yii2\Authentication\IdentityInterface;

/**
 * Class IdentityMock
 * @package Wearesho\Yii2\Authentication\Tests\Mocks
 */
class IdentityMock implements IdentityInterface
{
    public function getId(): int
    {
    }

    public function validatePassword(string $password): bool
    {
    }

    public static function findIdentityByLogin(string $login): ?IdentityInterface
    {
    }
}
