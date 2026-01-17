<?php

namespace App\Services\Backend;

use App\Models\AccessGroup;
use App\Services\BaseService;

class AccessGroupService extends BaseService
{
    /**
     * Create a new access group.
     */
    public function createAccessGroup(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $accessGroup = AccessGroup::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $accessGroup);
        }, 'Access Group creation failed');
    }

    /**
     * Update an existing access group.
     */
    public function updateAccessGroup(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $accessGroup = AccessGroup::find($id);
            if (!$accessGroup) {
                return $this->error(trans('Access group not found'), [], 404);
            }
            $accessGroup->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $accessGroup);
        }, 'Access Group update failed');
    }

    /**
     * Delete an access group.
     */
    public function deleteAccessGroup(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $accessGroup = AccessGroup::find($id);
            if (!$accessGroup) {
                return $this->error(trans('Access group not found'), [], 404);
            }
            $accessGroup->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Access Group deletion failed');
    }
}
