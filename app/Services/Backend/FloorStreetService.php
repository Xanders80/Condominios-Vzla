<?php

namespace App\Services\Backend;

use App\Models\FloorStreet;
use App\Services\BaseService;

class FloorStreetService extends BaseService
{
    /**
     * Create a new floor/street.
     */
    public function createFloorStreet(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $floorStreet = FloorStreet::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $floorStreet);
        }, 'Floor/Street creation failed');
    }

    /**
     * Update an existing floor/street.
     */
    public function updateFloorStreet(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $floorStreet = FloorStreet::find($id);
            if (!$floorStreet) {
                return $this->error(trans('Floor/Street not found'), [], 404);
            }
            $floorStreet->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $floorStreet);
        }, 'Floor/Street update failed');
    }

    /**
     * Delete a floor/street.
     */
    public function deleteFloorStreet(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $floorStreet = FloorStreet::find($id);
            if (!$floorStreet) {
                return $this->error(trans('Floor/Street not found'), [], 404);
            }
            $floorStreet->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Floor/Street deletion failed');
    }
}
