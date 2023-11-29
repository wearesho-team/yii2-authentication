<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\Tests;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii\Http;

/**
 * Class LogoutFormTest
 * @package Wearesho\Yii2\Authentication\Tests
 */
class LogoutFormTest extends TestCase
{
    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Invalid data type: stdClass. Wearesho\Yii2\Authorization\Repository is expected.
     */
    public function testInvalidRepositoryDependency(): void
    {
        new Authentication\LogoutForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => new \stdClass(),
            ]
        );
    }

    public function testMissingRefreshToken(): void
    {
        $form = new Authentication\LogoutForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => $this->createMock(Authorization\Repository::class),
            ]
        );
        $form->refresh = null;
        $this->assertFalse(
            $form->validate()
        );
        $this->assertArrayHasKey('refresh', $form->getErrors());
    }

    public function testInvalidRefreshToken(): void
    {
        $form = new Authentication\LogoutForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            [
                'repository' => $repository = $this->createMock(Authorization\Repository::class),
            ]
        );

        $repository->expects($this->once())
            ->method('delete')
            ->with('token')
            ->willReturn(null);

        $form->detachBehaviors();

        $form->refresh = 'token';
        try {
            $form->getResponse();
        } catch (Http\Exceptions\HttpValidationException $exception) {
            $this->assertEquals(
                'refresh is invalid.',
                $form->getFirstError('refresh')
            );
        }
        $this->assertInstanceOf(Http\Exceptions\HttpValidationException::class, $exception);
    }

    public function testValidRefreshTokenLogout(): void
    {
        $form = new Authentication\LogoutForm(
            $this->createMock(Http\Request::class),
            new Http\Response([
                'charset' => 'utf8',
            ]),
            [
                'repository' => $repository = $this->createMock(Authorization\Repository::class),
            ]
        );
        $form->refresh = 'token';
        $form->detachBehaviors();

        $repository->expects($this->once())
            ->method('delete')
            ->with('token')
            ->willReturn(1);

        $response = $form->getResponse();
        $this->assertEquals(205, $response->getStatusCode());
    }
}
