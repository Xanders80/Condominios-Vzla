<?php

namespace App\Services\Backend;

use App\Models\InterestRate;
use App\Services\BaseService;

class InterestRateService extends BaseService
{
    public function createRate(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $rate = InterestRate::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $rate);
        }, 'Interest Rate creation failed');
    }

    public function updateRate(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $rate = InterestRate::find($id);
            if (!$rate) return $this->error(trans('Interest Rate not found'), [], 404);
            $rate->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $rate);
        }, 'Interest Rate update failed');
    }

    public function deleteRate(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $rate = InterestRate::find($id);
            if (!$rate) return $this->error(trans('Interest Rate not found'), [], 404);
            $rate->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Interest Rate deletion failed');
    }
}
