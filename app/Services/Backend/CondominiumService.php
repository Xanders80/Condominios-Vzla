<?php

namespace App\Services\Backend;

use App\Models\Condominiums;
use App\Models\PostalCodeAddress;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class CondominiumService extends BaseService
{
    /**
     * Create a new condominium.
     */
    public function createCondominium(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $data['active'] = isset($data['active']) ? 1 : 0;
            $condominium = Condominiums::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $condominium);
        }, 'Condominium creation failed');
    }

    /**
     * Update an existing condominium.
     */
    public function updateCondominium(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $condominium = Condominiums::find($id);
            if (!$condominium) {
                return $this->error(trans('Condominium not found'), [], 404);
            }
            $data['active'] = isset($data['active']) ? 1 : 0;
            $condominium->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $condominium);
        }, 'Condominium update failed');
    }

    /**
     * Delete a condominium.
     */
    public function deleteCondominium(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $condominium = Condominiums::find($id);
            if (!$condominium) {
                return $this->error(trans('Condominium not found'), [], 404);
            }
            $condominium->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Condominium deletion failed');
    }

    /**
     * Get addresses by zone search term.
     */
    public function getAddressByZone(string $searchTerm): array
    {
        $postalCodes = PostalCodeAddress::where('postal_zone.name', 'LIKE', '%' . $searchTerm . '%')
            ->selectRaw("postal_zone.id, CONCAT(postal_zone.name, ', Parroquia ', parishes.name, ', Ciudad ', MAX(cities.name), ', Municipio ', municipalities.name, ', Estado ', states.name, ', VE, Código Postal: ', postal_zone.zip_code) AS data")
            ->join('parishes', 'postal_zone.parish_id', '=', 'parishes.id')
            ->join('municipalities', 'parishes.municipality_id', '=', 'municipalities.id')
            ->join('cities', 'municipalities.id', '=', 'cities.municipality_id')
            ->join('states', 'cities.state_id', '=', 'states.id')
            ->groupBy(
                'postal_zone.id',
                'postal_zone.name',
                'parishes.name',
                'municipalities.name',
                'states.name',
                'postal_zone.zip_code'
            )
            ->pluck('data', 'postal_zone.id');

        if ($postalCodes->isEmpty()) {
            return $this->error('No se encontraron direcciones, verifique su búsqueda.', [], 404);
        }

        return $this->success('Addresses found', $postalCodes->toArray());
    }
}
