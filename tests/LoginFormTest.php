<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\Tests;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii\Http;

/**
 * Class LoginFormTest
 * @package Wearesho\Yii2\Authentication\Tests
 */
class LoginFormTest extends TestCase
{
    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Invalid data type: stdClass. Wearesho\Yii2\Authorization\Repository is expected.
     */
    public function testInvalidRepositoryDependency(): void
    {
        new Authentication\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => new \stdClass(),
            ]
        );
    }

    public function testMissingLogin(): void
    {
        $form = new Authentication\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => $this->createMock(Authorization\Repository::class),
            ]
        );
        $form->login = null;
        $this->assertFalse(
            $form->validate(['login'])
        );
    }

    public function testMissingPassword(): void
    {
        $form = new Authentication\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => $this->createMock(Authorization\Repository::class),
            ]
        );
        $form->password = null;
        $this->assertFalse(
            $form->validate(['password'])
        );
    }

    public function testInvalidLogin(): void
    {
        $form = new Authentication\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => $this->createMock(Authorization\Repository::class),
            ]
        );
        $form->login = 'invalid';
        $form->password = 'password';

        $form->identityClass = Mock\Identity::class;
        Mock\Identity::$willFind = null;

        try {
            $form->getResponse();
        } catch (Http\Exceptions\HttpValidationException $exception) {
            $this->assertEquals(
                'login is invalid.',
                $form->getFirstError('login')
            );
        }
        $this->assertInstanceOf(Http\Exceptions\HttpValidationException::class, $exception);
    }

    public function testInvalidPassword(): void
    {
        $form = new Authentication\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => $this->createMock(Authorization\Repository::class),
            ]
        );
        $form->login = 'invalid';
        $form->password = 'password';

        $form->identityClass = Mock\Identity::class;
        Mock\Identity::$willFind = $identity = $this->createMock(Mock\Identity::class);

        $identity->expects($this->once())
            ->method('validatePassword')
            ->with('password')
            ->willReturn(false);

        try {
            $form->getResponse();
        } catch (Http\Exceptions\HttpValidationException $exception) {
            $this->assertEquals(
                'password is invalid.',
                $form->getFirstError('password')
            );
        }
        $this->assertInstanceOf(Http\Exceptions\HttpValidationException::class, $exception);
    }

    public function testGeneratingToken(): void
    {
        $form = new Authentication\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => $repository = $this->createMock(Authorization\Repository::class),
            ]
        );
        $form->login = 'invalid';
        $form->password = 'password';

        $form->identityClass = Mock\Identity::class;
        Mock\Identity::$willFind = $identity = $this->createMock(Mock\Identity::class);

        $identity->expects($this->once())
            ->method('validatePassword')
            ->with('password')
            ->willReturn(true);

        $identity->expects($this->once())
            ->method('getId')
            ->willReturn($userId = 2);

        $repository->expects($this->once())
            ->method('create')
            ->with($userId)
            ->willReturn($token = new Authorization\Token('Access', 'Refresh'));

        $response = $form->getResponse();
        $this->assertEquals(
            [
                'id' => $userId,
                'access' => $token->getAccess(),
                'refresh' => $token->getRefresh(),
            ],
            $response->data
        );
    }
}
