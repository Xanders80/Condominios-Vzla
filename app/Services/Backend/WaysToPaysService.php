<?php

namespace App\Services\Backend;

use App\Models\WaysToPays;
use App\Services\BaseService;

class WaysToPaysService extends BaseService
{
    /**
     * Create a new way to pay.
     */
    public function createWaysToPays(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $record = WaysToPays::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $record);
        }, 'WaysToPays creation failed');
    }

    /**
     * Update an existing way to pay.
     */
    public function updateWaysToPays(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $record = WaysToPays::find($id);
            if (!$record) {
                return $this->error(trans('WaysToPays not found'), [], 404);
            }
            $record->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $record);
        }, 'WaysToPays update failed');
    }

    /**
     * Delete a way to pay.
     */
    public function deleteWaysToPays(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $record = WaysToPays::find($id);
            if (!$record) {
                return $this->error(trans('WaysToPays not found'), [], 404);
            }
            $record->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'WaysToPays deletion failed');
    }
}
