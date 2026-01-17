<?php

namespace App\Services\Backend;

use App\Models\Supplier;
use App\Services\BaseService;

class SupplierService extends BaseService
{
    public function createSupplier(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $supplier = Supplier::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $supplier);
        }, 'Supplier creation failed');
    }

    public function updateSupplier(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $supplier = Supplier::find($id);
            if (!$supplier) return $this->error(trans('Supplier not found'), [], 404);
            $supplier->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $supplier);
        }, 'Supplier update failed');
    }

    public function deleteSupplier(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $supplier = Supplier::find($id);
            if (!$supplier) return $this->error(trans('Supplier not found'), [], 404);
            $supplier->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Supplier deletion failed');
    }
}
