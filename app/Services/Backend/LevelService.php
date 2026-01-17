<?php

namespace App\Services\Backend;

use App\Models\Level;
use App\Services\BaseService;

class LevelService extends BaseService
{
    /**
     * Create a new level.
     */
    public function createLevel(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $data['access'] = $this->makeLevelArray($data['access'] ?? []);
            $level = Level::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $level);
        }, 'Level creation failed');
    }

    /**
     * Update an existing level.
     */
    public function updateLevel(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $level = Level::find($id);
            if (!$level) {
                return $this->error(trans('Level not found'), [], 404);
            }
            if (isset($data['access'])) {
                $data['access'] = $this->makeLevelArray($data['access']);
            }
            $level->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $level);
        }, 'Level update failed');
    }

    /**
     * Delete a level.
     */
    public function deleteLevel(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $level = Level::find($id);
            if (!$level) {
                return $this->error(trans('Level not found'), [], 404);
            }
            $level->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Level deletion failed');
    }

    /**
     * Format the level access array based on global config.
     */
    private function makeLevelArray(array $accessRequest): array
    {
        $levels = [];
        foreach (collect(config('master.app.level')) as $level) {
            $levels[$level] = collect($accessRequest)->contains($level);
        }

        return $levels;
    }
}
