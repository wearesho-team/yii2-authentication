<?php

namespace Wearesho\Yii2\Authentication\TwoFactor;

use Wearesho\Yii\Http;
use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii2\Token;
use yii\di;
use yii\base;

/**
 * Class ConfirmForm
 * @package Wearesho\Yii2\Authentication\TwoFactor
 */
class ConfirmForm extends Http\Form
{
    /** @var string */
    public $hash;

    /** @var string */
    public $value;

    /** @var string */
    public $identityClass = Authentication\IdentityInterface::class;

    /** @var string|array|Token\Repository */
    public $tokenRepository = Token\Repository::class;

    /** @var string|array|Authorization\Repository */
    public $authorizationRepository = Authorization\Repository::class;

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
                ['hash', 'value',],
                'required',
            ],
            [
                ['hash', 'value',],
                'string',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function generateResponse(): array
    {
        $token = $this->tokenRepository->get($this->hash);
        if (empty($token)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            Http\Exceptions\HttpValidationException::addAndThrow(
                'hash',
                \Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => 'hash',
                ]),
                $this
            );
        }

        if ($token->getValue() !== $this->value) {
            /** @noinspection PhpUnhandledExceptionInspection */
            Http\Exceptions\HttpValidationException::addAndThrow(
                'value',
                \Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => 'value',
                ]),
                $this
            );
        }

        $identity = call_user_func([$this->identityClass, 'findIdentityByLogin'], $token->getOwner());
        if (!$identity instanceof Authentication\IdentityInterface) {
            // todo: throw exception
        }

        $id = $identity->getId();
        $accessToken = $this->authorizationRepository->create($id);

        $this->response->statusCode = 202;

        return Authentication\View::render($id, $accessToken);
    }
}
