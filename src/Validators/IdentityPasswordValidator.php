<?php

namespace Wearesho\Yii2\Authentication\Validators;

use Wearesho\Yii2\Authentication;
use yii\base\InvalidConfigException;
use yii\validators\Validator;
use yii\web\IdentityInterface;

/**
 * Class IdentityPasswordValidator
 * @package Wearesho\Yii2\Authentication\Validators
 */
class IdentityPasswordValidator extends Validator
{
    /** @var  string */
    public $targetAttribute;

    /** @var  string */
    public $loginAttribute;

    /** @var Authentication\Interfaces\IdentitiesRepositoryInterface */
    protected $repository;

    /**
     * IdentityPasswordValidator constructor.
     * @param Authentication\Interfaces\IdentitiesRepositoryInterface $repository
     * @param array $config
     */
    public function __construct(Authentication\Interfaces\IdentitiesRepositoryInterface $repository, array $config = [])
    {
        parent::__construct($config);

        $this->repository = $repository;
    }

    public function init(): void
    {
        parent::init();

        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @throws InvalidConfigException
     */
    public function validateAttribute($model, $attribute): void
    {
        if (empty($model->{$this->loginAttribute})) {
            throw new InvalidConfigException("Login attribute must be specified.");
        }

        $identity = $this->repository->pull($model->{$this->loginAttribute}, $model->{$attribute});
        if (!$identity instanceof IdentityInterface) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        if (!empty($this->targetAttribute)) {
            $model->{$this->targetAttribute} = $identity;
        }
    }
}