<?php

namespace Wearesho\Yii2\Authentication;

use Wearesho\Yii\Http;
use Wearesho\Yii2\Authorization;
use yii\filters;

/**
 * Class Controller
 * @package Wearesho\Yii2\Authentication
 */
class Controller extends Http\Controller
{
    /**
     * Class name that implements
     * @see IdentityInterface
     * @var string
     */
    public $identityClass;

    public $accessControl = [
        'get' => [
            'class' => AccessControl\Panel::class,
        ],
        'post' => [
            'class' => AccessControl\Form::class,
        ],
    ];

    /** @var string|array|Authorization\Repository */
    public $repository = Authorization\Repository::class;

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
                ],
            ],
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function actions(): array
    {
        $actions = [
            'index' => [
                'post' => [
                    'class' => LoginForm::class,
                    'repository' => $this->repository,
                    'identityClass' => $this->identityClass,
                ],
                'delete' => [
                    'class' => LogoutForm::class,
                    'repository' => $this->repository,
                ],
                'put' => [
                    'class' => RefreshForm::class,
                    'repository' => $this->repository,
                ],
            ],
            'access-control' => $this->accessControl,
        ];
        return \array_merge_recursive(parent::actions(), $actions);
    }
}
