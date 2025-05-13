<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Payments extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = ['id', 'dweller_id', 'nro_confirmation', 'amount', 'image', 'banks_id', 'condominiums_id', 'ways_to_pays_id', 'date_pay', 'date_confirm', 'conciliated', 'observations'];
    protected $casts = [];
    protected $table = 'payments';

    public function dweller()
    {
        return $this->belongsTo(Dweller::class, 'dweller_id', 'id');
    }

    public function banks()
    {
        return $this->belongsTo(Banks::class, 'banks_id', 'id');
    }

    public function condominiums()
    {
        return $this->belongsTo(Condominiums::class, 'condominiums_id', 'id');
    }

    public function waystopays()
    {
        return $this->belongsTo(WaysToPays::class, 'ways_to_pays_id', 'id');
    }

    /**
     * Relación polimórfica para un archivo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function scopeFilterLevel($query)
    {
        // Obtener el nivel del usuario autenticado
        $level = auth()->user()->level->code;

        // Filtrar por el usuario actual si no es 'root' y es 'user'
        if ($level != 'root' && $level == 'user') {
            $query->where('dweller_id', Dweller::where('email', auth()->user()->email)->value('id'));
        }

        // Asegurarse de que el dweller_id no sea nulo
        $query->where('dweller_id', '!=', 'null');

        return $query;
    }

    /**
     * Common validation rules for Condominios.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data, bool $isUpdate = false)
    {
        return Validator::make($data, [
            'dweller_id' => 'exists:dwellers,id' . ($isUpdate ? '' : '|required'),
            'nro_confirmation' => 'required' . ($isUpdate ? '' : '|unique:Payments,nro_confirmation'),
            'amount' => 'required',
            'banks_id' => 'required|exists:banks,id',
            'condominiums_id' => 'required|exists:condominiums,id',
            'ways_to_pays_id' => 'required|exists:ways_to_pays,id',
            'date_pay' => 'required',
            'date_confirm' => 'required',
            'observations' => 'required|between:3,500',
        ]);
    }

    // CRUD Operations
    private static function handleOperation(callable $operation, array $data = [], $id = null, $validate = true)
    {
        $response = [];

        try {
            if ($validate) {
                $validator = self::validationRules($data, $id ? true : false);
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
