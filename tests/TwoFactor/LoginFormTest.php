<?php

namespace Wearesho\Yii2\Authentication\Tests\TwoFactor;

use PHPUnit\Framework\TestCase;
use Wearesho\Token\Generator;
use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authentication\Tests\Mock;
use Wearesho\Yii2\Token;

/**
 * Class LoginFormTest
 * @package Wearesho\Yii2\Authentication\Tests\TwoFactor
 */
class LoginFormTest extends TestCase
{
    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Invalid data type: stdClass. Wearesho\Yii2\Authentication\TwoFactor\ConfigInterface is
     */
    public function testInvalidConfigDependency(): void
    {
        new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => new \stdClass,
            ]
        );
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Invalid data type: stdClass. Wearesho\Yii2\Token\Repository is expected.
     */
    public function testInvalidRepositoryDependency(): void
    {
        new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => Authentication\TwoFactor\EnvironmentConfig::class,
                'repository' => new \stdClass,
            ]
        );
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Invalid data type: stdClass. Wearesho\Token\Generator
     */
    public function testInvalidTokenGeneratorDependency(): void
    {
        new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => Authentication\TwoFactor\EnvironmentConfig::class,
                'repository' => $this->createMock(Token\Repository::class),
                'tokenGenerator' => new \stdClass(),
            ]
        );
    }

    public function testMissingLogin(): void
    {
        $form = new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => Authentication\TwoFactor\EnvironmentConfig::class,
                'repository' => $this->createMock(Token\Repository::class),
                'tokenGenerator' => new Generator\Char(1, []),
            ]
        );
        $this->assertFalse($form->validate(['login']));
    }

    public function testMissingPassword(): void
    {
        $form = new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => Authentication\TwoFactor\EnvironmentConfig::class,
                'repository' => $this->createMock(Token\Repository::class),
                'tokenGenerator' => new Generator\Char(1, []),
            ]
        );
        $this->assertFalse($form->validate(['password']));
    }

    public function testInvalidLogin(): void
    {
        $form = new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => Authentication\TwoFactor\EnvironmentConfig::class,
                'repository' => $this->createMock(Token\Repository::class),
                'tokenGenerator' => new Generator\Char(1, []),
            ]
        );
        $form->login = 'test';
        $form->password = 'test';

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
        $form = new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => Authentication\TwoFactor\EnvironmentConfig::class,
                'repository' => $this->createMock(Token\Repository::class),
                'tokenGenerator' => new Generator\Char(1, []),
            ]
        );
        $form->login = 'test';
        $form->password = 'test';

        $form->identityClass = Mock\Identity::class;
        Mock\Identity::$willFind = $identity = $this->createMock(Mock\Identity::class);

        $identity->expects($this->once())
            ->method('validatePassword')
            ->with('test')
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

    public function testSuccess(): void
    {
        $form = new Authentication\TwoFactor\LoginForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'config' => $this->createMock(Authentication\TwoFactor\EnvironmentConfig::class),
                'repository' => $repository = $this->createMock(Token\Repository::class),
                'tokenGenerator' => new Generator\Char(6, ['a']),
            ]
        );
        $form->login = 'test';
        $form->password = 'test';

        $form->identityClass = Mock\Identity::class;
        Mock\Identity::$willFind = $identity = $this->createMock(Mock\Identity::class);

        $identity->expects($this->once())
            ->method('validatePassword')
            ->with('test')
            ->willReturn(true);

        $repository->expects($this->once())
            ->method('put')
            ->willReturn($hash = 'testHash');

        $token = null;

        $form->on(
            Authentication\TwoFactor\LoginForm::EVENT_AFTER_CREATE,
            function (Authentication\TwoFactor\Events\Create $event) use (&$token) {
                $token = $event->getToken()->getValue();
            }
        );

        $response = $form->getResponse();
        $this->assertEquals(['hash' => $hash,], $response->data);
        $this->assertEquals('aaaaaa', $token);
    }
}
