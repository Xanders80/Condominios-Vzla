<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Banks extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = ['id', 'code_sudebank', 'name_ibp', 'rif', 'website', 'active'];
    protected $casts = [];
    protected $table = 'banks';
    protected $appends = ['name'];
    protected $primaryKey = 'id';

    public function scopeFilterLevel($query)
    {
        return $query->selectRaw('id, CONCAT(code_sudebank, " - ", name_ibp) as nameBank')
            ->where('active', true);
    }

    public function getNameAttribute()
    {
        return $this->code_sudebank . ' - ' . $this->name_ibp;
    }

    /**
     * Common validation rules for Banks.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data, bool $isUpdate = false)
    {
        return Validator::make($data, [
            'code_sudebank' => 'max:4|required' . ($isUpdate ? '' : '|unique:banks,code_sudebank'),
            'name_ibp' => 'required|between:3,150',
            'rif' => 'required|min:12',
            'website' => 'required|url',
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
