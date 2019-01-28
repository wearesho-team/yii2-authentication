<?php

namespace Wearesho\Yii2\Authentication\Tests\Http;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication\Http\LogoutForm;
use Wearesho\Yii2\Authorization\Repository;

/**
 * Class LogoutFormTest
 * @package Wearesho\Yii2\Authentication\Tests\Http
 */
class LogoutFormTest// extends TestCase
{
    /**
     * @expectedException \Wearesho\Yii\Http\Exceptions\HttpValidationException
     */
    public function testInvalid(): void
    {
        $form = new LogoutForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            $this->createMock(Repository::class)
        );

        $form->refresh = 'test';

        $form->getResponse();
    }

    public function testSuccess(): void
    {
        $form = new LogoutForm(
            $this->createMock(Http\Request::class),
            $this->createMock(Http\Response::class),
            $this->createConfiguredMock(Repository::class, [
                'delete' => 1,
            ])
        );

        $form->refresh = 'test';

        $response = $form->getResponse();
        $this->assertEquals([], $response->data);
    }
}
