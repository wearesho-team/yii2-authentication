<?php

namespace Wearesho\Yii2\Authentication\Tests\Http;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication\Http\LoginForm;
use Wearesho\Yii2\Authentication\Interfaces\IdentitiesRepositoryInterface;
use Wearesho\Yii2\Authentication\Tests\Mocks\IdentityMock;
use Wearesho\Yii2\Authentication\Tests\Mocks\RepositoryMock;
use Wearesho\Yii2\Authorization\Repository;

/**
 * Class LoginFormTest
 * @package Wearesho\Yii2\Authentication\Tests\Http
 */
class LoginFormTest// extends TestCase
{
    public function testResponse(): void
    {
        //\Yii::$container->set(IdentitiesRepositoryInterface::class, RepositoryMock::class);
        $form = new LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'authorizationRepository' => $this->createMock(Repository::class),
                'identitiesRepository' => new RepositoryMock(),
            ]
        );

        $form->login = 'test';
        $form->password = 'test';

        $response = $form->getResponse();
        $this->assertArraySubset(['id' => 1,], $response->data);
    }
}
