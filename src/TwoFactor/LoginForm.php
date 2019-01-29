<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Token;
use yii\base;
use yii\di;

/**
 * Class LoginForm
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class LoginForm extends Http\Form
{
    public const EVENT_AFTER_CREATE = 'afterCreate';

    /** @var string */
    public $login;

    /** @var string */
    public $password;

    /** @var string */
    public $identityClass = Authentication\IdentityInterface::class;

    /** @var string|array|Token\Repository */
    public $repository = Token\Repository::class;

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->repository = di\Instance::ensure($this->repository, Token\Repository::class);
    }

    public function rules(): array
    {
        return [
            [
                ['login', 'password',],
                'required',
            ],
            [
                ['login', 'password',],
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
        if (!$identity instanceof Authentication\IdentityInterface) {
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

        // Todo: implement token generation
        $value = 111111;

        $token = new Token\Entity(
            'login',
            $this->login,
            $value,
            (new \DateTime())->add(new \DateInterval('PT30M'))
        );

        $hash = $this->repository->put($token);

        $this->trigger(static::EVENT_AFTER_CREATE, new Events\Create($value));

        $this->response->statusCode = 201;

        return ['hash' => $hash,];
    }
}
