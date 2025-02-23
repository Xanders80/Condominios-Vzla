<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AccessMenu extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['id', 'access_group_id', 'menu_id', 'access'];

    protected $casts = [
        'id' => 'string',
        'access' => 'array',
    ];

    /**
     * Define la relación con el modelo AccessGroup.
     */
    public function access_group(): object
    {
        return $this->belongsTo(AccessGroup::class, 'access_group_id', 'id');
    }

    /**
     * Define la relación con el modelo Menu.
     */
    public function menu(): object
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    /**
     * Valida los datos de entrada para almacenar datos.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data)
    {
        return Validator::make($data, [
            'access_group_id' => 'required|exists:access_groups,id',
            'menu_id' => 'required',
        ]);
    }

    // CRUD Operations
    private static function handleOperation(callable $operation, array $data = [], $id = null, $validate = true)
    {
        $response = [];

        try {
            if ($validate) {
                $validator = self::validationRules($data);
                if ($validator->fails()) {
                    $response = [
                        'status' => false,
                        'message' => trans(config('constants.MESSAGES.MESS_BAD_REQUEST')),
                        'errors' => $validator->errors()->toArray(),
                        'status_code' => config('constants.STATUS_CODES.BAD_REQUEST'),
                    ];
                    Log::error('Error fn(BanksModel) updateData', [
                        'detail' => $response['message'],
                        'error' => trans('Validation failed while updating data.'),
                        'data' => 'Data: ' . json_encode($response['errors']),
                    ]);
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
