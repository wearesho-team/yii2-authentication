<?php

namespace Wearesho\Yii2\Authentication\Http;

use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii\Http;
use yii\di;

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

    /** @var Authentication\IdentityInterface */
    public $identity;

    /** @var string */
    public $identityClass = Authentication\IdentityInterface::class;

    /** @var array|string|Authorization\Repository */
    public $authorizationRepository = Authorization\Repository::class;

    public function init(): void
    {
        parent::init();

        $this->authorizationRepository = di\Instance::ensure(
            $this->authorizationRepository,
            Authorization\Repository::class
        );
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
            [
                ['password',],
                Authentication\Validators\IdentityPasswordValidator::class,
                'targetAttribute' => 'identity',
                'loginAttribute' => 'login',
                'identityClass' => $this->identityClass,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function generateResponse(): array
    {
        $token = $this->authorizationRepository->create($this->identity->getId());

        $this->response->statusCode = 202;

        return [
            'id' => $this->identity->getId(),
            'access' => $token->getAccess(),
            'refresh' => $token->getRefresh(),
        ];
    }
}
