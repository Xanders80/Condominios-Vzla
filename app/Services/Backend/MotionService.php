<?php

namespace App\Services\Backend;

use App\Events\MotionVoteCast;
use App\Models\Motion;
use App\Services\BaseService;

class MotionService extends BaseService
{
    public function createMotion(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $motion = Motion::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $motion);
        }, 'Motion creation failed');
    }

    public function updateMotion(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $motion = Motion::find($id);
            if (!$motion) return $this->error(trans('Motion not found'), [], 404);
            $motion->update($data);

            // Broadcast the update for real-time results
            broadcast(new MotionVoteCast($motion->fresh()));

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $motion);
        }, 'Motion update failed');
    }

    public function deleteMotion(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $motion = Motion::find($id);
            if (!$motion) return $this->error(trans('Motion not found'), [], 404);
            $motion->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Motion deletion failed');
    }
}
