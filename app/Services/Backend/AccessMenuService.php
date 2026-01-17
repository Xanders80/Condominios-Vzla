<?php

namespace App\Services\Backend;

use App\Models\AccessGroup;
use App\Models\AccessMenu;
use App\Services\BaseService;

class AccessMenuService extends BaseService
{
    /**
     * Sync access menus for a specific access group.
     */
    public function syncAccessMenu(string $accessGroupId, array $menuIds): array
    {
        return $this->executeTransaction(function () use ($accessGroupId, $menuIds) {
            $accessGroup = AccessGroup::find($accessGroupId);
            if (!$accessGroup) {
                return $this->error(trans('Access group not found'), [], 404);
            }

            // Remove existing associations
            AccessMenu::whereAccessGroupId($accessGroupId)->forceDelete();

            // Prepare new data
            $data = collect($menuIds)->map(fn($menuId) => [
                'access_group_id' => $accessGroupId,
                'menu_id' => $menuId,
                'access' => [], // Default empty or based on request if extended
            ]);

            // Create new associations
            $accessGroup->access_menu()->createMany($data->toArray());

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')));
        }, 'Access Menu synchronization failed');
    }

    /**
     * Delete all access menu associations for a group.
     */
    public function deleteAccessMenuForGroup(string $accessGroupId): array
    {
        return $this->executeTransaction(function () use ($accessGroupId) {
            AccessMenu::whereAccessGroupId($accessGroupId)->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Access Menu deletion failed');
    }
}
