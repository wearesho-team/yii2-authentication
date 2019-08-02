<?php

declare(strict_types=1);

namespace Wearesho\Yii2\Authentication\AccessControl;

use Wearesho\Yii\Http;
use yii\rbac;
use yii\base;
use yii\web;
use yii\di;

/**
 * Class Panel
 * @package Wearesho\Yii2\Authentication\AccessControl
 */
class Panel extends Http\Panel
{
    /** @var string|array|rbac\ManagerInterface */
    public $authManager = 'authManager';

    /** @var string|array|web\User reference */
    public $user = 'user';

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->authManager = di\Instance::ensure($this->authManager, rbac\ManagerInterface::class);
        $this->user = di\Instance::ensure($this->user, web\User::class);
    }

    /**
     * @return array
     * @throws web\ForbiddenHttpException
     */
    protected function generateResponse(): array
    {
        $id = $this->user->id;
        if (is_null($id)) {
            throw new web\ForbiddenHttpException();
        }

        $roles = $this->getRoles($id);
        $permissions = array_merge($roles, $this->getPermissions($id));

        return compact('roles', 'permissions');
    }

    /**
     * @param int $id
     * @return string[]
     */
    protected function getRoles(int $id): array
    {
        $roles = $this->authManager->getRolesByUser($id);

        foreach ($roles as $role) {
            $roles = array_merge($roles, $this->authManager->getChildRoles($role->name));
        }

        return array_unique(array_keys($roles));
    }

    protected function getPermissions(int $id): array
    {
        return array_keys(array_map(function (rbac\Permission $permission): string {
            return $permission->name;
        }, $this->authManager->getPermissionsByUser($id)));
    }
}
