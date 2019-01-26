<?php

namespace Wearesho\Yii2\Authentication\Tests\Validators;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii2\Authentication;
use yii\base;
use yii\web\IdentityInterface;

/**
 * Class IdentityPasswordValidatorTest
 * @package Wearesho\Yii2\Authentication\Tests\Validators
 */
class IdentityPasswordValidatorTest extends TestCase
{
    /** @var Authentication\Tests\Mocks\RepositoryMock */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new Authentication\Tests\Mocks\RepositoryMock();
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Login attribute must be specified
     */
    public function testMissingLoginAttribute(): void
    {
        $validator = new Authentication\Validators\IdentityPasswordValidator($this->repository);
        $validator->validateAttribute(new base\Model(), 'password');
    }

    public function testInvalidResult(): void
    {
        $this->repository->shouldFail = true;
        $validator = new Authentication\Validators\IdentityPasswordValidator($this->repository, [
            'loginAttribute' => 'login',
        ]);

        $model = new Authentication\Tests\Mocks\FormMock([
            'login' => 'test',
            'password' => 'test',
        ]);
        $validator->validateAttribute($model, 'password');
        $this->assertCount(1, $model->errors);
        $this->assertArraySubset(['password' => ['Password is invalid.',],], $model->errors);
    }

    public function testWithoutSettingTargetAttribute(): void
    {
        $validator = new Authentication\Validators\IdentityPasswordValidator($this->repository, [
            'loginAttribute' => 'login',
        ]);

        $model = new Authentication\Tests\Mocks\FormMock([
            'login' => 'test',
            'password' => 'test',
        ]);

        $validator->validateAttribute($model, 'password');
        $this->assertEmpty($model->errors);
        $this->assertNull($model->target);
    }

    public function testWithSettingTargetAttribute(): void
    {
        $validator = new Authentication\Validators\IdentityPasswordValidator($this->repository, [
            'loginAttribute' => 'login',
            'targetAttribute' => 'target',
        ]);

        $model = new Authentication\Tests\Mocks\FormMock([
            'login' => 'test',
            'password' => 'test',
        ]);

        $validator->validateAttribute($model, 'password');
        $this->assertEmpty($model->errors);
        $this->assertNotEmpty($model->target);
        $this->assertInstanceOf(IdentityInterface::class, $model->target);
        $this->assertEquals(1, $model->target->getId());
    }
}
