<?php

namespace App\Services\Backend;

use App\Models\CommonArea;
use App\Services\BaseService;

class CommonAreaService extends BaseService
{
    public function createArea(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $area = CommonArea::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $area);
        }, 'Common Area creation failed');
    }

    public function updateArea(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $area = CommonArea::find($id);
            if (!$area) return $this->error(trans('Common Area not found'), [], 404);
            $area->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $area);
        }, 'Common Area update failed');
    }

    public function deleteArea(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $area = CommonArea::find($id);
            if (!$area) return $this->error(trans('Common Area not found'), [], 404);
            $area->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Common Area deletion failed');
    }
}
