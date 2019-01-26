<?php


namespace Wearesho\Yii2\Authentication\Interfaces;

use yii\web\IdentityInterface;

/**
 * Interface IdentitiesRepositoryInterface
 * @package Wearesho\Yii2\Authentication\Interfaces
 */
interface IdentitiesRepositoryInterface
{
    /**
     * @param string $login
     * @param string $password
     * @return null|IdentityInterface
     */
    public function pull(string $login, string $password);
}
