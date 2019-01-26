<?php

namespace Wearesho\Yii2\Authentication\Tests\Mocks;

use yii\web;

/**
 * Class IdentityMock
 * @package Wearesho\Yii2\Authentication\Tests\Mocks
 */
class IdentityMock implements web\IdentityInterface
{
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function findIdentity($id)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }
}