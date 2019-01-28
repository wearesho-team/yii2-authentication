<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication;

/**
 * Class RefreshForm
 * @package Wearesho\Yii2\Authentication\Http
 */
class RefreshForm extends LogoutForm
{
    protected function map(int $userId): array
    {
        $token = $this->repository->create($userId);
        return View::render($userId, $token);
    }
}
