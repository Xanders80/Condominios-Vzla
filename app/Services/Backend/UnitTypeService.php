<?php

namespace App\Services\Backend;

use App\Models\UnitType;
use App\Services\BaseService;

class UnitTypeService extends BaseService
{
    /**
     * Create a new unit type.
     */
    public function createUnitType(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $unitType = UnitType::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $unitType);
        }, 'Unit type creation failed');
    }

    /**
     * Update an existing unit type.
     */
    public function updateUnitType(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $unitType = UnitType::find($id);
            if (!$unitType) {
                return $this->error(trans('Unit type not found'), [], 404);
            }
            $unitType->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $unitType);
        }, 'Unit type update failed');
    }

    /**
     * Delete a unit type.
     */
    public function deleteUnitType(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $unitType = UnitType::find($id);
            if (!$unitType) {
                return $this->error(trans('Unit type not found'), [], 404);
            }
            $unitType->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Unit type deletion failed');
    }
}
