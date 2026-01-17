<?php

namespace App\Services\Backend;

use App\Models\Debt;
use App\Services\BaseService;

class DebtService extends BaseService
{
    public function createDebt(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $debt = Debt::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $debt);
        }, 'Debt creation failed');
    }

    public function updateDebt(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $debt = Debt::find($id);
            if (!$debt) return $this->error(trans('Debt not found'), [], 404);
            $debt->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $debt);
        }, 'Debt update failed');
    }

    public function deleteDebt(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $debt = Debt::find($id);
            if (!$debt) return $this->error(trans('Debt not found'), [], 404);
            $debt->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Debt deletion failed');
    }
}
