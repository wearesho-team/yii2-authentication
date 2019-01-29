<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\Tests;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii\Http;

/**
 * Class RefreshTokenTest
 * @package Wearesho\Yii2\Authentication\Tests
 */
class RefreshFormTest extends TestCase
{
    public function testGeneratingNewTokenPair(): void
    {
        $form = new Authentication\RefreshForm(
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
            ->willReturn($userId = 1);

        $repository->expects($this->once())
            ->method('create')
            ->with($userId)
            ->willReturn($token = new Authorization\Token('access', 'refresh'));

        $response = $form->getResponse();
        $this->assertEquals(205, $response->getStatusCode());

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
