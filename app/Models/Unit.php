<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Unit extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = ['id', 'name', 'unit_type_id', 'dweller_id', 'tower_sector_id', 'floor_street_id', 'status'];
    protected $casts = [];
    protected $table = 'units';

    // Definir la relación con UnitType
    public function unitType()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id', 'id');
    }

    // Definir la relación con Dweller
    public function dweller()
    {
        return $this->belongsTo(Dweller::class, 'dweller_id', 'id');
    }

    // Definir la relación con TowerSector
    public function towerSector()
    {
        return $this->belongsTo(TowerSector::class, 'tower_sector_id', 'id');
    }

    // Definir la relación con FloorStreet
    public function floorStreet()
    {
        return $this->belongsTo(FloorStreet::class, 'floor_street_id', 'id');
    }

    public function scopeFilterLevel($query)
    {
        // Obtener todos los registros de Dweller que coinciden con el email del usuario autenticado
        $dwellers = Dweller::where('email', auth()->user()->email)->get();

        // Si necesitas solo los IDs de los dwellers, puedes hacer esto:
        $dwellerIds = $dwellers->pluck('id');

        $level = auth()->user()->level->code;
        if ($level != 'root' && $level == 'user') {
            // Filtrar por el ID del dweller del usuario autenticado
            $query->whereIn('dweller_id', $dwellerIds);
        }
        $query->where('dweller_id', '!=', 'null');

        return $query;
    }

    /**
     * Common validation rules for Condominios.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|between:3,50',
            'unit_type_id' => 'required|exists:unit_types,id',
            'dweller_id' => 'required|exists:dwellers,id',
            'tower_sector_id' => 'required|exists:tower_sectors,id',
            'floor_street_id' => 'required|exists:floor_streets,id',
        ]);
    }

    // CRUD Operations
    private static function handleOperation(callable $operation, array $data = [], $id = null, $validate = true)
    {
        $response = [];

        try {
            if ($validate) {
                $validator = self::validationRules($data);
                Log::info('Validator Content', ['validator' => $validator]);
                if ($validator->fails()) {
                    $response = [
                        'status' => false,
                        'message' => trans(config('constants.MESSAGES.MESS_BAD_REQUEST')),
                        'errors' => $validator->errors()->toArray(),
                        'status_code' => config('constants.STATUS_CODES.BAD_REQUEST'),
                    ];
                    Log::error('Validation failed', ['errors' => $response['errors']]);
                    return $response;
                }
                $operation($data, $id);
                $response = [
                    'status' => true,
                    'message' => trans(config('constants.MESSAGES.MESS_CREATED')),
                    'status_code' => config('constants.STATUS_CODES.OK'),
                ];
            } else {
                $operation($id);
                $response = [
                    'status' => true,
                    'message' => trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')),
                    'status_code' => config('constants.STATUS_CODES.OK'),
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getMessage(),
                'status_code' => config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'),
            ];
            Log::error(trans('Data Operation failed'), $response);
        }

        return $response;
    }

    public static function createData(array $data)
    {
        return self::handleOperation(function ($data) {
            self::create($data);
        }, $data);
    }

    public static function updateData($id, array $data)
    {
        return self::handleOperation(function ($data, $id) {
            optional(self::find($id))->update($data);
        }, $data, $id);
    }

    public static function deleteData($id)
    {
        return self::handleOperation(function ($id) {
            optional(self::find($id))->delete();
        }, [], $id, false);
    }
}
