<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Token;
use yii\filters;

/**
 * Class Controller
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class Controller extends Authentication\Controller
{
    /** @var string|array|Token\Repository */
    public $tokenRepository;

    /** @var string|array|TokenGeneratorInterface */
    public $tokenGenerator;

    /**
     * @codeCoverageIgnore
     */
    public function behaviors(): array
    {
        return array_merge_recursive(parent::behaviors(), [
            'access' => [
                'class' => filters\AccessControl::class,
                'rules' => [
                    [
                        'class' => filters\AccessRule::class,
                        'actions' => ['index',],
                        'verbs' => ['PATCH',],
                        'allow' => true,
                        'permissions' => '?',
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
        return array_merge_recursive(parent::actions(), [
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
            ],
        ]);
    }
}
