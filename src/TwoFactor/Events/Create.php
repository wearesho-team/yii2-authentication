<?php

namespace Wearesho\Yii2\Authentication\TwoFactor\Events;

use yii\base;

/**
 * Class Create
 * @package Wearesho\Yii2\Authentication\TwoFactor\Events
 */
class Create extends base\Event
{
    /** @var string */
    protected $value;

    public function __construct(string $value, array $config = [])
    {
        parent::__construct($config);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
