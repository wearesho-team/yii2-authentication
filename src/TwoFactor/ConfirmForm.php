<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii2\Token;
use yii\base;
use yii\di;
use yii\web;

/**
 * Class ConfirmForm
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class ConfirmForm extends Http\Panel
{
    /** @var string */
    public $hash;

    /** @var string */
    public $token;

    /** @var string */
    public $login;

    /** @var string */
    public $identityClass = Authentication\IdentityInterface::class;

    /** @var string|array|Token\Repository */
    public $tokenRepository = Token\Repository::class;

    /** @var string|array|Authorization\Repository */
    public $authorizationRepository = Authorization\Repository::class;

    public function behaviors(): array
    {
        return [
            'tokenValidation' => [
                'class' => Token\ValidationBehavior::class,
                'repository' => $this->tokenRepository,
                'hash' => 'hash',
                'token' => 'token',
                'tokenOwner' => 'login',
                'type' => 'login',
            ],
        ];
    }

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        $this->tokenRepository = di\Instance::ensure($this->tokenRepository, Token\Repository::class);
        $this->authorizationRepository = di\Instance::ensure(
            $this->authorizationRepository,
            Authorization\Repository::class
        );
    }

    public function rules(): array
    {
        return [
            [
                ['hash', 'token', 'login',],
                'required',
            ],
            [
                ['hash', 'token', 'login',],
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
            throw new web\HttpException(409, 'Hash and token were correct, but user not found');
        }

        $id = $identity->getId();
        $token = $this->authorizationRepository->create($id);

        $this->response->statusCode = 202;

        return Authentication\View::render($id, $token);
    }
}
