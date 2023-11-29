<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Token;
use Wearesho\Token\Generator;
use yii\base;
use yii\di;

/**
 * Class LoginForm
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class LoginForm extends Http\Panel
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

    /** @var string|array|Generator */
    public $tokenGenerator = Generator::class;

    /** @var string|array|ConfigInterface */
    public $config = ConfigInterface::class;

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->config = di\Instance::ensure($this->config, ConfigInterface::class);
        $this->repository = di\Instance::ensure($this->repository, Token\Repository::class);
        $this->tokenGenerator = di\Instance::ensure($this->tokenGenerator, Generator::class);
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

        $value = $this->tokenGenerator->generate();
        $ttl = $this->config->getTokenLifetime();
        $ttlInterval = new \DateInterval("PT{$ttl}S");

        $token = new TokenEntity(
            $this->request->userIP ?? '',
            $this->login,
            $value,
            $ttlInterval
        );
        $hash = $this->repository->put($token);

        $this->trigger(static::EVENT_AFTER_CREATE, new Events\Create($token));
        $this->response->statusCode = 201;

        $response = compact('hash', 'ttl');
        if (YII_DEBUG) {
            $response['value'] = $token->getValue();
        }

        return $response;
    }
}
