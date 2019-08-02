<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\AccessControl;

use Wearesho\Yii\Http;
use yii\base;
use yii\web;
use yii\di;

/**
 * Class Form
 * @package Wearesho\Yii2\Authentication
 */
class Form extends Http\Panel
{
    /** @var string|array|web\User reference */
    public $user = 'user';

    /** @var string */
    public $permission;

    /** @var array|null */
    public $params;

    public function rules(): array
    {
        return [
            [['permission',], 'required',],
            [['permission',], 'string',],
            [['params',], 'each', 'rule' => ['safe',],],
        ];
    }

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->user = di\Instance::ensure($this->user, web\User::class);
    }

    /**
     * @return array
     * @throws web\ForbiddenHttpException
     */
    protected function generateResponse(): array
    {
        if (!$this->user->can($this->permission, $this->params ?? [])) {
            throw new web\ForbiddenHttpException;
        }
        $this->response->statusCode = 204;
        return [];
    }
}
