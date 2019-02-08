<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Token;
use Wearesho\Token\Generator;
use yii\filters;

/**
 * Class Controller
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class Controller extends Authentication\Controller
{
    /** @var string|array|Token\Repository */
    public $tokenRepository = Token\Repository::class;

    /** @var string|array|Generator */
    public $tokenGenerator = Generator::class;

    /**
     * @codeCoverageIgnore
     */
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => filters\AccessControl::class,
                'rules' => [
                    [
                        'class' => filters\AccessRule::class,
                        'actions' => ['index',],
                        'verbs' => ['POST'],
                        'allow' => true,
                        'roles' => ['?',],
                    ],
                    [
                        'class' => filters\AccessRule::class,
                        'actions' => ['index',],
                        'verbs' => ['PUT', 'DELETE',],
                        'allow' => true,
                        'roles' => ['@',],
                    ],
                    [
                        'class' => filters\AccessRule::class,
                        'actions' => ['index',],
                        'verbs' => ['PATCH',],
                        'allow' => true,
                        'permissions' => ['?'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function actions(): array
    {
        return [
            'index' => [
                'post' => [
                    'class' => LoginForm::class,
                    'identityClass' => $this->identityClass,
                    'repository' => $this->tokenRepository,
                    'tokenGenerator' => $this->tokenGenerator,
                ],
                'patch' => [
                    'class' => ConfirmForm::class,
                    'identityClass' => $this->identityClass,
                    'authorizationRepository' => $this->repository,
                    'tokenRepository' => $this->tokenRepository,
                ],
                'delete' => [
                    'class' => Authentication\LogoutForm::class,
                    'repository' => $this->repository,
                ],
                'put' => [
                    'class' => Authentication\RefreshForm::class,
                    'repository' => $this->repository,
                ],
            ],
        ];
    }
}
