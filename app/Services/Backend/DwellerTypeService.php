<?php

namespace App\Services\Backend;

use App\Models\DwellerType;
use App\Services\BaseService;

class DwellerTypeService extends BaseService
{
    /**
     * Create a new dweller type.
     */
    public function createDwellerType(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $dwellerType = DwellerType::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $dwellerType);
        }, 'Dweller type creation failed');
    }

    /**
     * Update an existing dweller type.
     */
    public function updateDwellerType(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $dwellerType = DwellerType::find($id);
            if (!$dwellerType) {
                return $this->error(trans('Dweller type not found'), [], 404);
            }
            $dwellerType->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $dwellerType);
        }, 'Dweller type update failed');
    }

    /**
     * Delete a dweller type.
     */
    public function deleteDwellerType(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $dwellerType = DwellerType::find($id);
            if (!$dwellerType) {
                return $this->error(trans('Dweller type not found'), [], 404);
            }
            $dwellerType->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Dweller type deletion failed');
    }
}
