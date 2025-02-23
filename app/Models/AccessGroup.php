<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AccessGroup extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $casts = [
        'id' => 'string',
    ];

    protected $fillable = ['id', 'name', 'code'];

    public function users(): object
    {
        return $this->hasMany(User::class, 'access_group_id', 'id');
    }

    public function access_menu(): object
    {
        return $this->hasMany(AccessMenu::class, 'access_group_id', 'id');
    }

    public function scopeCanAccess($query, $crud): bool
    {
        $route = explode('.', Route::currentRouteName())[0];
        $menu = Menu::where('code', $route)->first();

        return $menu ? $query->whereHas('access_menu', function ($query) use ($menu, $crud) {
            $query->whereMenuId($menu->id)
                ->whereAccessGroupId($this->id)
                ->whereJsonContains('access', $crud);
        })->exists() : false;
    }

    public function scopeFilterLevel($query): object
    {
        $level = auth()->user()->level->code;
        if ($level !== 'root') {
            $query->whereNotIn('code', ['root']);
        }

        return $query;
    }

    public static function validationRules(array $data)
    {
        return Validator::make($data, [
            'name' => 'required',
            'code' => 'required',
        ]);
    }

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
