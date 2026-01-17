<?php

namespace App\Services\Backend;

use App\Models\Unit;
use App\Services\BaseService;

class UnitService extends BaseService
{
    /**
     * Create a new unit.
     */
    public function createUnit(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $data['status'] = isset($data['status']) ? 1 : 0;
            $unit = Unit::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $unit);
        }, 'Unit creation failed');
    }

    /**
     * Update an existing unit.
     */
    public function updateUnit(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $unit = Unit::find($id);
            if (!$unit) {
                return $this->error(trans('Unit not found'), [], 404);
            }
            $data['status'] = isset($data['status']) ? 1 : 0;
            $unit->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $unit);
        }, 'Unit update failed');
    }

    /**
     * Delete a unit.
     */
    public function deleteUnit(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $unit = Unit::find($id);
            if (!$unit) {
                return $this->error(trans('Unit not found'), [], 404);
            }
            $unit->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Unit deletion failed');
    }
}
