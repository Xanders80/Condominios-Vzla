<?php

namespace App\Services\Backend;

use App\Models\Banks;
use App\Services\BaseService;

class BankService extends BaseService
{
    /**
     * Create a new bank.
     */
    public function createBank(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $bank = Banks::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $bank);
        }, 'Bank creation failed');
    }

    /**
     * Update an existing bank.
     */
    public function updateBank(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $bank = Banks::find($id);
            if (!$bank) {
                return $this->error(trans('Bank not found'), [], 404);
            }
            $bank->update($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_UPDATED', 'Record updated successfully')), $bank);
        }, 'Bank update failed');
    }

    /**
     * Delete a bank.
     */
    public function deleteBank(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $bank = Banks::find($id);
            if (!$bank) {
                return $this->error(trans('Bank not found'), [], 404);
            }
            $bank->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Bank deletion failed');
    }
}
