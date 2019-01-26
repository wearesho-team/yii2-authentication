<?php

namespace Wearesho\Yii2\Authentication\Http;

use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii\Http;
use yii\db;
use yii\web;

/**
 * Class LoginForm
 * @package Wearesho\Yii2\Authentication\Http
 */
class LoginForm extends Http\Form
{
    /** @var web\IdentityInterface */
    public $identity;

    /** @var Authorization\Repository */
    protected $authorizationRepository;

    public function __construct(
        Http\Request $request,
        Http\Response $response,
        db\Connection $connection,
        Authorization\Repository $repository,
        array $config = []
    ) {
        parent::__construct($request, $response, $connection, $config);
        $this->authorizationRepository = $repository;
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
