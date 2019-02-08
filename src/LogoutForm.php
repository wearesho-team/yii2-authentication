<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication;

use Wearesho\Yii2\Authorization;
use Wearesho\Yii\Http;
use yii\di;
use yii\base;

/**
 * Class LogoutForm
 * @package Wearesho\Yii2\Authentication\Http
 */
class LogoutForm extends Http\Panel
{
    /** @var string */
    public $refresh;

    /** @var array|string|Authorization\Repository */
    public $repository = [
        'class' => Authorization\Repository::class,
    ];

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->repository = di\Instance::ensure($this->repository, Authorization\Repository::class);
    }

    public function behaviors(): array
    {
        return [
            'getParams' => [
                'class' => Http\Behaviors\GetParamsBehavior::class,
                'attributes' => ['refresh',],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['refresh',], 'required',],
            [['refresh',], 'string',],
        ];
    }

    /**
     * @inheritdoc
     */
    final protected function generateResponse(): array
    {
        $userId = $this->repository->delete($this->refresh);
        if (\is_null($userId)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            Http\Exceptions\HttpValidationException::addAndThrow(
                'refresh',
                \Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => 'refresh',
                ]),
                $this
            );
        }

        $this->response->statusCode = 205;

        return $this->map($userId);
    }

    /**
     * This method can be overriden in child classes
     *
     * @param int $userId
     * @return array
     */
    protected function map(int $userId): array
    {
        return [];
    }
}
