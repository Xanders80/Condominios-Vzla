<?php

namespace App\Services\Backend;

use App\Models\BanksCondominium;
use App\Services\BaseService;

class BanksCondominiumService extends BaseService
{
    /**
     * Create a new bank-condominium relationship.
     */
    public function createBanksCondominium(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $record = BanksCondominium::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $record);
        }, 'BanksCondominium creation failed');
    }

    /**
     * Update an existing bank-condominium relationship.
     */
    public function updateBanksCondominium(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $record = BanksCondominium::find($id);
            if (!$record) {
                return $this->error(trans('BanksCondominium not found'), [], 404);
            }
            $record->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $record);
        }, 'BanksCondominium update failed');
    }

    /**
     * Delete a bank-condominium relationship.
     */
    public function deleteBanksCondominium(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $record = BanksCondominium::find($id);
            if (!$record) {
                return $this->error(trans('BanksCondominium not found'), [], 404);
            }
            $record->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'BanksCondominium deletion failed');
    }
}
