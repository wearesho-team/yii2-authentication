<?php

namespace Wearesho\Yii2\Authentication\Tests\Mocks;

use Wearesho\Yii2\Authentication;

/**
 * Class RepositoryMock
 * @package Wearesho\Yii2\Authentication\Tests\Mocks
 */
class RepositoryMock implements Authentication\Interfaces\IdentitiesRepositoryInterface
{
    public $shouldFail = false;

    /**
     * @inheritdoc
     */
    public function pull(string $login, string $password)
    {
        if ($this->shouldFail) {
            return null;
        }

        return new Authentication\Tests\Mocks\IdentityMock(1);
    }
}
