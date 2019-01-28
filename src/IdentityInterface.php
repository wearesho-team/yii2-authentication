<?php

namespace Wearesho\Yii2\Authentication;

/**
 * Interface IdentityInterface
 * @package Wearesho\Yii2\Authentication
 */
interface IdentityInterface
{
    public function getId(): int;

    public function validatePassword(string $password): bool;

    public static function findIdentityByLogin(string $login): ?IdentityInterface;
}
