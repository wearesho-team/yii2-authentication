<?php

namespace Wearesho\Yii2\Authentication\Tests\TwoFactor;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authentication\Tests\Mock;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii2\Token;
use yii\web;

/**
 * Class ConfirmFormTest
 * @package Wearesho\Yii2\Authentication\Tests\TwoFactor
 */
class ConfirmFormTest extends TestCase
{
    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Invalid data type: stdClass. Wearesho\Yii2\Token\Repository is expected.
     */
    public function testInvalidTokenRepositoryDependency(): void
    {
        new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'tokenRepository' => new \stdClass,
            ]
        );
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Invalid data type: stdClass. Wearesho\Yii2\Authorization\Repository is expected.
     */
    public function testInvalidAuthorizationRepositoryDependency(): void
    {
        new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'tokenRepository' => $this->createMock(Token\Repository::class),
                'authorizationRepository' => new \stdClass,
            ]
        );
    }

    public function testMissingHash(): void
    {
        $form = new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'tokenRepository' => $this->createMock(Token\Repository::class),
                'authorizationRepository' => $this->createMock(Authorization\Repository::class),
            ]
        );
        $this->assertFalse($form->validate(['hash']));
    }

    public function testMissingValue(): void
    {
        $form = new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'tokenRepository' => $this->createMock(Token\Repository::class),
                'authorizationRepository' => $this->createMock(Authorization\Repository::class),
            ]
        );
        $this->assertFalse($form->validate(['value']));
    }

    public function testInvalidHash(): void
    {
        $form = new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'tokenRepository' => $tokenRepository = $this->createMock(Token\Repository::class),
                'authorizationRepository' => $this->createMock(Authorization\Repository::class),
            ]
        );

        $form->hash = 'test';
        $form->value = 'test';

        $tokenRepository->expects($this->once())
            ->method('get')
            ->willReturn(null);

        try {
            $form->getResponse();
        } catch (Http\Exceptions\HttpValidationException $exception) {
            $this->assertEquals(
                'hash is invalid.',
                $form->getFirstError('hash')
            );
        }

        $this->assertInstanceOf(Http\Exceptions\HttpValidationException::class, $exception);
    }

    public function testInvalidValue(): void
    {
        $form = new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'tokenRepository' => $tokenRepository = $this->createMock(Token\Repository::class),
                'authorizationRepository' => $this->createMock(Authorization\Repository::class),
            ]
        );

        $form->hash = 'test';
        $form->value = 'test';

        $tokenRepository->expects($this->once())
            ->method('get')
            ->willReturn(
                new Token\Entity('test', 'test', 'invalid', new \DateTime())
            );

        try {
            $form->getResponse();
        } catch (Http\Exceptions\HttpValidationException $exception) {
            $this->assertEquals(
                'value is invalid.',
                $form->getFirstError('value')
            );
        }

        $this->assertInstanceOf(Http\Exceptions\HttpValidationException::class, $exception);
    }

    public function testMissingUserWithCorrectValues(): void
    {
        $form = new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'identityClass' => Mock\Identity::class,
                'tokenRepository' => $tokenRepository = $this->createMock(Token\Repository::class),
                'authorizationRepository' => $this->createMock(Authorization\Repository::class),
            ]
        );

        $form->hash = 'test';
        $form->value = 'test';

        $tokenRepository->expects($this->once())
            ->method('get')
            ->willReturn(
                new Token\Entity('test', 'test', $form->value, new \DateTime())
            );

        $form->identityClass = Mock\Identity::class;
        Mock\Identity::$willFind = null;

        try {
            $form->getResponse();
        } catch (web\HttpException $exception) {
            $this->assertEquals(409, $exception->statusCode);
            $this->assertEquals('Hash and token were correct, but user not found', $exception->getMessage());
        }

        $this->assertInstanceOf(web\HttpException::class, $exception);
    }

    public function testCorrectResult(): void
    {
        $form = new Authentication\TwoFactor\ConfirmForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'identityClass' => Mock\Identity::class,
                'tokenRepository' => $tokenRepository = $this->createMock(Token\Repository::class),
                'authorizationRepository' => $authRepository = $this->createMock(Authorization\Repository::class),
            ]
        );

        $form->hash = 'test';
        $form->value = 'test';

        $tokenRepository->expects($this->once())
            ->method('get')
            ->willReturn(
                new Token\Entity('test', 'test', $form->value, new \DateTime())
            );

        $form->identityClass = Mock\Identity::class;
        Mock\Identity::$willFind = $identity = $this->createMock(Mock\Identity::class);

        $identity->expects($this->once())
            ->method('getId')
            ->willReturn($userId = 10);

        $authRepository->expects($this->once())
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
