<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Level extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'access' => 'array',
    ];
    protected $fillable = [
        'name',
        'access',
        'code',
    ];

    public function canAccess($level): bool
    {
        return $this->access[$level] ?? false;
    }

    public static function makeLevelArray($request): array
    {
        $levels = [];
        foreach (collect(config('master.app.level')) as $level) {
            $levels[$level] = collect($request->access)->contains($level);
        }

        return $levels;
    }

    public function scopeFilterLevel($query): object
    {
        $level = auth()->user()->level->code;
        if ($level != 'root') {
            $query->whereNotIn('code', ['root']);
        }

        return $query;
    }

    /**
     * Valida los datos de entrada para almacenar una nueva FAQ.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data)
    {
        return Validator::make($data, [
            'name' => 'required',
            'code' => 'required',
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
