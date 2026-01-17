<?php

namespace App\Services\Backend;

use App\Models\Menu;
use App\Services\BaseService;

class MenuService extends BaseService
{
    /**
     * Create a new menu item with its access permissions.
     */
    public function createMenu(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $menu = Menu::create($data);

            if (isset($data['access_group_id'])) {
                $this->handleAccessMenu($menu, $data['access_group_id'], $data);
            }

            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $menu);
        }, 'Menu creation failed');
    }

    /**
     * Update an existing menu item and its access permissions.
     */
    public function updateMenu(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $menu = Menu::find($id);
            if (!$menu) {
                return $this->error(trans('Menu not found'), [], 404);
            }

            $menu->update($data);

            if (isset($data['access_group_id'])) {
                $menu->access_menu()->delete();
                $this->handleAccessMenu($menu, $data['access_group_id'], $data);
            }

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $menu);
        }, 'Menu update failed');
    }

    /**
     * Delete a menu item.
     */
    public function deleteMenu(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $menu = Menu::find($id);
            if (!$menu) {
                return $this->error(trans('Menu not found'), [], 404);
            }
            $menu->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Menu deletion failed');
    }

    /**
     * Reorder menu items recursively.
     */
    public function reorderMenu(array $sortData): array
    {
        return $this->executeTransaction(function () use ($sortData) {
            $this->loopUpdateMenu($sortData);
            return $this->success(trans('El menú se ha ordenado correctamente.'));
        }, 'Menu reordering failed');
    }

    /**
     * Recursive helper to update menu order and parent relationships.
     */
    private function loopUpdateMenu(array $menuItems, ?string $parentId = null): void
    {
        foreach ($menuItems as $key => $item) {
            $menu = Menu::find($item->id);
            if ($menu) {
                $menu->update([
                    'parent_id' => $parentId,
                    'sort' => $key + 1
                ]);

                if (isset($item->children) && count($item->children) > 0) {
                    $this->loopUpdateMenu($item->children, $item->id);
                }
            }
        }
    }

    /**
     * Handle access menu relationships.
     */
    private function handleAccessMenu($menu, array $accessGroupIds, array $data): void
    {
        $accessMenuData = [];
        foreach ($accessGroupIds as $accessGroupId) {
            $accessMenuData[] = [
                'menu_id' => $menu->id,
                'access_group_id' => $accessGroupId,
                'access' => $data['access_crud_' . $accessGroupId] ?? [],
            ];
        }

        if (!empty($accessMenuData)) {
            $menu->access_menu()->createMany($accessMenuData);
        }
    }
}
