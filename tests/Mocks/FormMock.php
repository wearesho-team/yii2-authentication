<?php

namespace Wearesho\Yii2\Authentication\Tests\Mocks;

use yii\base;
use yii\web;

/**
 * Class FormMock
 * @package Wearesho\Yii2\Authentication\Tests\Mocks
 */
class FormMock extends base\Model
{
    /** @var string */
    public $login;

    /** @var string */
    public $password;

    /** @var web\IdentityInterface */
    public $target;
}
