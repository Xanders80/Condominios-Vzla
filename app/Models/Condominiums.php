<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Condominiums extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'name_incharge',
        'jobs_incharge',
        'email',
        'rif',
        'phone',
        'address_line',
        'postal_code_address',
        'reserve_found',
        'rate_percentage',
        'billing_date',
        'active',
        'observations',
        'logo',
    ];

    protected $table = 'condominiums';

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Accessors and Mutators
    public function getNameAttribute($value)
    {
        return ucfirst(strtolower($value));
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getEmailAttribute($value)
    {
        return strtolower($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function getRifAttribute($value)
    {
        return strtoupper($value);
    }

    public function setRifAttribute($value)
    {
        $this->attributes['rif'] = strtoupper($value);
    }

    public static function validationRules(array $data, $id, bool $isUpdate = false)
    {
        return Validator::make($data, [
            'name' => 'required|string|distinct|between:3,50' . ($isUpdate ? '' : '|unique:condominiums,name'),
            'name_incharge' => 'required|string|min:3|max:100',
            'jobs_incharge' => 'required|string|min:3|max:100',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                "unique:condominiums,email" . ($isUpdate ? ",$id" : "")
            ],
            'rif' => "required|string|min:12|unique:condominiums,rif" . ($isUpdate ? ",$id" : ""),
            'phone' => 'required|string|regex:/^\(\d{3,4}\) \d{3}-\d{4}$/',
            'address_line' => 'required|string|between:3,255',
            'postal_code_address' => 'required|string|max:20',
            'reserve_found' => 'required|numeric|min:0',
            'rate_percentage' => 'required|numeric|between:0,100',
            'billing_date' => 'required|numeric|min:1|max:31',
            'observations' => 'nullable|string|max:500',
            'logo' => 'nullable|string|max:255',
        ], [
            'rif.min' => 'El Número de Rif no tiene un formato válido',
            'phone.regex' => 'El Número de Teléfono no tiene un formato válido',
        ]);
    }


    // CRUD Operations
    private static function handleOperation(callable $operation, array $data = [], $id = null, $validate = true)
    {
        $response = [];

        try {
            if ($validate) {
                $validator = self::validationRules($data, $id, $id ? true : false);
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
                'message' => trans(config('constants.MESSAGES.DATA_VALIDATED_ERROR')),
                'errors' => [$e->getMessage()],
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

    // Search Functionality
    public static function search($query)
    {
        return self::where('name', 'like', '%' . $query . '%')
            ->orWhere('rif', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->get();
    }
}
