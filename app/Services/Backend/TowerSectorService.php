<?php

namespace App\Services\Backend;

use App\Models\TowerSector;
use App\Services\BaseService;

class TowerSectorService extends BaseService
{
    /**
     * Create a new tower/sector.
     */
    public function createTowerSector(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $towerSector = TowerSector::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $towerSector);
        }, 'Tower/Sector creation failed');
    }

    /**
     * Update an existing tower/sector.
     */
    public function updateTowerSector(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $towerSector = TowerSector::find($id);
            if (!$towerSector) {
                return $this->error(trans('Tower/Sector not found'), [], 404);
            }
            $towerSector->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $towerSector);
        }, 'Tower/Sector update failed');
    }

    /**
     * Delete a tower/sector.
     */
    public function deleteTowerSector(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $towerSector = TowerSector::find($id);
            if (!$towerSector) {
                return $this->error(trans('Tower/Sector not found'), [], 404);
            }
            $towerSector->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Tower/Sector deletion failed');
    }
}
