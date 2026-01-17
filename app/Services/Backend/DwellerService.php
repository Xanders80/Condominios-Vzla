<?php

namespace App\Services\Backend;

use App\Models\Dweller;
use App\Services\BaseService;

class DwellerService extends BaseService
{
    /**
     * Create a new dweller.
     */
    public function createDweller(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $dweller = Dweller::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $dweller);
        }, 'Dweller creation failed');
    }

    /**
     * Update an existing dweller.
     */
    public function updateDweller(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $dweller = Dweller::find($id);
            if (!$dweller) {
                return $this->error(trans('Dweller not found'), [], 404);
            }
            $dweller->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $dweller);
        }, 'Dweller update failed');
    }

    /**
     * Delete a dweller.
     */
    public function deleteDweller(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $dweller = Dweller::find($id);
            if (!$dweller) {
                return $this->error(trans('Dweller not found'), [], 404);
            }
            $dweller->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Dweller deletion failed');
    }
}
