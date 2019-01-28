<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication;

use Wearesho\Yii2\Authorization;
use Wearesho\Yii\Http;
use yii\di;
use yii\base;

/**
 * Class LoginForm
 * @package Wearesho\Yii2\Authentication\Http
 */
class LoginForm extends Http\Panel
{
    /** @var string */
    public $login;

    /** @var string */
    public $password;

    /** @var array|string|Authorization\Repository */
    public $repository = [
        'class' => Authorization\Repository::class,
    ];

    /** @var string */
    public $identityClass = IdentityInterface::class;

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->repository = di\Instance::ensure($this->repository, Authorization\Repository::class);
    }

    public function rules(): array
    {
        return [
            [
                ['login', 'password',],
                'required',
            ],
            [
                ['login', 'password'],
                'string',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function generateResponse(): array
    {
        $identity = call_user_func([$this->identityClass, 'findIdentityByLogin'], $this->login);
        if (!$identity instanceof IdentityInterface) {
            /** @noinspection PhpUnhandledExceptionInspection */
            Http\Exceptions\HttpValidationException::addAndThrow(
                'login',
                \Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => 'login',
                ]),
                $this
            );
        }
        if (!$identity->validatePassword($this->password)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            Http\Exceptions\HttpValidationException::addAndThrow(
                'password',
                \Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => 'password',
                ]),
                $this
            );
        }

        $id = $identity->getId();
        $token = $this->repository->create($id);

        $this->response->statusCode = 202;

        return View::render($id, $token);
    }
}
