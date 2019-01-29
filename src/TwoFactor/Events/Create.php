<?php

namespace Wearesho\Yii2\Authentication\TwoFactor\Events;

use Wearesho\Yii2\Token;
use yii\base;

/**
 * Class Create
 * @package Wearesho\Yii2\Authentication\TwoFactor\Events
 */
class Create extends base\Event
{
    /** @var Token\Entity */
    protected $token;

    public function __construct(Token\Entity $token, array $config = [])
    {
        parent::__construct($config);
        $this->token = $token;
    }

    public function getToken(): Token\Entity
    {
        return $this->token;
    }
}
