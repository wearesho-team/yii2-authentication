<?php

namespace Wearesho\Yii2\Authentication\Validators;

use Wearesho\Yii2\Authentication;
use yii\base;
use yii\validators\Validator;

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

    /** @var Authentication\IdentityInterface */
    public $identityClass;

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
     * @throws base\InvalidConfigException
     */
    public function validateAttribute($model, $attribute): void
    {
        if (empty($model->{$this->loginAttribute})) {
            throw new base\InvalidConfigException("Login attribute must be specified.");
        }

        $identity = $this->identityClass::findIdentityByLogin($model->{$this->loginAttribute});
        if (!$identity instanceof Authentication\IdentityInterface) {
            // todo: fix message to custom
            $this->addError($model, $this->loginAttribute, $this->message);
            return;
        }

        if (!$identity->validatePassword($model->{$attribute})) {
            $this->addError($model, $attribute, $this->message);
        }

        if (!empty($this->targetAttribute)) {
            $model->{$this->targetAttribute} = $identity;
        }
    }
}
