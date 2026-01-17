<?php

namespace App\Services\Backend;

use App\Models\AssemblySession;
use App\Services\BaseService;

class AssemblyService extends BaseService
{
    public function createAssembly(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $assembly = AssemblySession::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $assembly);
        }, 'Assembly creation failed');
    }

    public function updateAssembly(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $assembly = AssemblySession::find($id);
            if (!$assembly) return $this->error(trans('Assembly not found'), [], 404);
            $assembly->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $assembly);
        }, 'Assembly update failed');
    }

    public function deleteAssembly(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $assembly = AssemblySession::find($id);
            if (!$assembly) return $this->error(trans('Assembly not found'), [], 404);
            $assembly->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Assembly deletion failed');
    }
}
