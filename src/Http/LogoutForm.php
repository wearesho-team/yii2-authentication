<?php

namespace Wearesho\Yii2\Authentication\Http;

use Wearesho\Yii2\Authorization;
use Wearesho\Yii\Http;
use yii\db;

/**
 * Class LogoutForm
 * @package Wearesho\Yii2\Authentication\Http
 */
class LogoutForm extends Http\Form
{
    /** @var string */
    public $refresh;

    /** @var Authorization\Repository */
    protected $authorizationRepository;

    public function rules(): array
    {
        return [
            [
                ['refresh',],
                'required',
            ],
            [
                ['refresh',],
                'string',
            ],
        ];
    }

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

    /**
     * @inheritdoc
     */
    protected function generateResponse(): array
    {
        $userId = $this->authorizationRepository->delete($this->refresh);
        if (is_null($userId)) {
            Http\Exceptions\HttpValidationException::addAndThrow(
                'refresh',
                'Refresh token is invalid',
                $this
            );
        }

        $this->response->statusCode = 205;

        return [];
    }
}
