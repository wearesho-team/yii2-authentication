<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication;

use Wearesho\Yii\Http;
use Wearesho\Yii2\Authorization;

/**
 * Class View
 * @package Wearesho\Yii2\Authentication
 */
class View extends Http\View
{
    /** @var int */
    protected $id;

    /** @var Authorization\Token */
    protected $token;

    public function __construct(int $id, Authorization\Token $token)
    {
        $this->id = $id;
        $this->token = $token;
    }

    protected function renderInstantiated(): array
    {
        return [
            'id' => $this->id,
            'access' => $this->token->getAccess(),
            'refresh' => $this->token->getRefresh(),
        ];
    }
}
