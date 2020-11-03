<?php

namespace mdm\admin\components;

use Yii;
use yii\rbac\Item;

/**
 *
 * @author Mohamed Elkayal <gemi2010@gmail.com>
 * @since 1.0
 */
class RoleHelper
{

    public static function getUserIdsByRoleRecurcively($roleName)
    {
        $authManager = Yii::$app->getAuthManager();
        $userIds = $authManager->getUserIdsByRole($roleName);
        $parents = RoleHelper::getParents($roleName);

        foreach ($parents as $parentRoleName) {
            $parentUserIds = $authManager->getUserIdsByRole($parentRoleName);
            $userIds = array_merge($userIds, $parentUserIds);
        }

        return $userIds;
    }

    private static function getParents($roleName, &$parents = [])
    {
        $authManager = Yii::$app->getAuthManager();

        foreach ($authManager->getRoles() as $parentRole) {
            $parentRoleName = $parentRole->name;
            foreach ($authManager->getChildren($parentRoleName) as $children) {
                if ($children->type == Item::TYPE_ROLE && $roleName == $children->name) {
                    $parents[] = $parentRoleName;
                    RoleHelper::getParents($parentRoleName, $parents);
                }
            }
        }
        
        return $parents;
    }
}

