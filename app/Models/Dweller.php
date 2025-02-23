<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Dweller extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const NAMES_LENGTH_RULE = 'between:3,50';
    public const NAMES_REGEX_RULE = 'regex:/^(?!.*\b(user|usuario|admin|administrador|administrator|adminis)\b)[\p{L} .]+$/uu';
    public const EMAIL_MAX_RULE = 'max:100';
    public const NAME_REGEX_MESSAGE = 'Solo se permite caracteres alfabéticos, espacios y no debe ser (user, admin) o algunas de sus variantes';
    public const ERROR_DATA_NOT_FOUND = 'Data not found';
    public const ERROR_VALIDATION_FAILED = 'Validation failed';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'document_type_id',
        'document_id',
        'email',
        'phone_number',
        'cell_phone_number',
        'dweller_type_id',
        'observations',
    ];

    protected $table = 'dwellers';
    protected $primaryKey = 'id';
    protected $appends = ['name'];

    public function payments()
    {
        return $this->hasMany(Payments::class, 'dweller_id', 'id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function dwellerType()
    {
        return $this->belongsTo(DwellerType::class, 'dweller_type_id');
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeFilterLevel($query)
    {
        $level = auth()->user()->level->code;
        if ($level === 'user') {
            $query->where('email', auth()->user()->email);
        }

        return $query->whereNotNull('email');
    }

    public static function validationRules(array $data, bool $isUpdate = false)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', self::NAMES_LENGTH_RULE, 'distinct', self::NAMES_REGEX_RULE],
            'last_name' => ['required', 'string', self::NAMES_LENGTH_RULE, 'distinct', self::NAMES_REGEX_RULE],
            'document_type_id' => 'required|exists:document_types,id',
            'phone_number' => 'required|min:15',
            'cell_phone_number' => 'required|min:15',
            'dweller_type_id' => 'required|exists:dweller_types,id',
            'observations' => 'required|between:3,500',
            'document_id' => 'required|distinct|numeric|min:1000001' . ($isUpdate ? '' : '|unique:dwellers,document_id'),
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                self::EMAIL_MAX_RULE,
                $isUpdate ? [] : ['unique:dwellers,email']
            ],
        ], [
            'first_name.regex' => self::NAME_REGEX_MESSAGE,
            'last_name.regex' => self::NAME_REGEX_MESSAGE,
            'document_id.numeric' => 'El documento debe ser un número.',
            'document_id.min' => 'El documento debe ser mayor a 1,000,000.',
            'phone_number.min' => 'El Número de Teléfono no tiene un formato válido',
            'cell_phone_number.min' => 'El Número del Móvil no tiene un formato válido',
            'email.email' => 'El formato del correo electrónico no es válido.',
        ]);
    }

    public static function getFirstDweller()
    {
        return self::first() ?? new self();
    }

    private static function handleOperation(callable $operation, array $data = [], $id = null, $validate = true)
    {
        $response = [];

        try {
            if ($validate) {
                $validator = self::validationRules($data, $id ? true : false);
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
