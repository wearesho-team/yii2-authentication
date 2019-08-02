<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\AccessControl;

use Wearesho\Yii\Http;
use yii\filters;

/**
 * Class Controller
 * @package Wearesho\Yii2\Authentication\AccessControl
 */
class Controller extends Http\Controller
{
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => filters\AccessControl::class,
                'rules' => [
                    [
                        'class' => filters\AccessRule::class,
                        'roles' => [Http\Behaviors\AccessControl::ROLE_DEFAULT,],
                        'actions' => ['index',],
                        'verbs' => ['GET', 'POST',],
                        'allow' => true,
                    ],
                ],
            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function actions(): array
    {
        $actions = [
            'index' => [
                'get' => Panel::class,
                'post' => Form::class,
            ],
        ];

        return \array_merge_recursive(parent::actions(), $actions);
    }
}
