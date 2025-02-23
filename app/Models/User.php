<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasUuids;
    use SoftDeletes;

    public const NAMES_LENGTH_RULE = 'between:3,50';
    public const NAMES_REGEX_RULE = 'regex:/^(?!.*\b(user|usuario|admin|administrador|administrator|adminis)\b)[\p{L} .]+$/uu';
    public const EMAIL_MAX_RULE = 'max:100';
    public const PASSW_REGEX_RULE = 'regex:/^(?=.*[0-9])(?=.*[#$.\-@*¬])[A-Z][A-Za-z0-9#$.\-@*¬]{8,14}$/';
    public const PASSW_NOTIN_RULE = 'not_in:';
    public const FISTN_MESS_REGEX = 'Solo se permite caracteres alfabéticos, espacios y no debe ser (user, admin) o algunas de sus variantes';
    public const PASSW_MESS_NOTIN = 'Escribiste una contraseña que están entre las 20 más comunes usadas en la web, por favor escribe una contraseña más segura';
    public const PASSW_MESS_REGEX = 'La contraseña debe contener entre 8 y 20 caracteres, incluyendo al menos un número, una letra y un símbolo de esta lista [#$%^&*()_\-+=@!,.<>?;:].';
    public const PASSC_MESS_SAME = 'La confirmación de la contraseña debe coincidir con la nueva contraseña.';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'level_id',
        'access_group_id',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'access_group_id',
        'level_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
        'first_name',
        'last_name',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'id' => 'string',
    ];

    protected $appends = ['name'];

    protected $primaryKey = 'id';

    public function level(): object
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    public function access_group(): object
    {
        return $this->belongsTo(AccessGroup::class, 'access_group_id', 'id');
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = $value ? bcrypt($value) : $this->password;
    }

    public function tokens(): object
    {
        return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable');
    }

    public function log(): object
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getCreateAttribute(): bool
    {
        return $this->access_group->canAccess('create');
    }

    public function getReadAttribute(): bool
    {
        return $this->access_group->canAccess('read');
    }

    public function getUpdateAttribute(): bool
    {
        return $this->access_group->canAccess('update');
    }

    public function getDeleteAttribute(): bool
    {
        return $this->access_group->canAccess('delete');
    }

    public function scopeFilterLevel($query)
    {
        $level = auth()->user()->level->code;
        if ($level != 'root') {
            if ($level == 'user') {
                $query->where('id', auth()->id());
            }
            $query->where('level_id', '!=', '1');
        }

        return $query;
    }

    public function getAllUserIdAttribute(): array
    {
        return $this->whereNotIn('level_id', [1])->pluck('id')->toArray();
    }

    public function hasEmailVerificationExpired()
    {
        return is_null($this->email_verified_at)
            && (!is_null($this->email_verification_sent_at)
                && abs(now()->diffInMinutes($this->email_verification_sent_at)) > config('auth.verification.expiration', 60));
    }

    public function sendEmailVerificationNotificationAt()
    {
        $this->email_verification_sent_at = now();
        $this->save();
    }

    public function invalidateEmailVerificationToken()
    {
        $this->email_verified_at = null;
        $this->save();
    }

    /**
     * Scope para calcular las estadísticas de usuarios verificados y pendientes.
     */
    public function scopeWithUserStatistics(Builder $query): Builder
    {
        return $query->selectRaw(
            'COUNT(*) as total_users,
            SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as verified_users,
            SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END) as pending_verification'
        );
    }

    /**
     *  Calcula el porcentaje dado el total y la parte.
     */
    protected function calculatePercentage(int $part, int $total): string
    {
        if ($total == 0) {
            return '0%';
        }

        return round(($part / $total) * 100) . '%';
    }

    public function calculateUserCounts()
    {
        $statistics = self::withUserStatistics()->first();
        $totalUsers = $statistics->total_users;
        $verifiedUsers = $statistics->verified_users;
        $pendingVerification = $statistics->pending_verification;

        return [
            'totalUsers' => $totalUsers,
            'verifiedUsers' => $verifiedUsers,
            'pendingVerification' => $pendingVerification,
            'verifiedPercentage' => $this->calculatePercentage($verifiedUsers, $totalUsers),
            'pendingPercentage' => $this->calculatePercentage($pendingVerification, $totalUsers),
        ];
    }

    /**
     * Validar los datos del registro de usuario.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validateRegister(array $data)
    {
        return self::commonValidation($data, 'register');
    }

    /**
     * Validar los datos del registro al agregar un nuevo usuario.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validateStore(array $data)
    {
        return self::commonValidation($data, 'store');
    }

    /**
     * Validar los datos del registro al modificar usuario.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validateUpdate(array $data, $idUser)
    {
        return self::commonValidation($data, 'update', $idUser);
    }

    /**
     * Validar los datos del Inicio de sesión de usuario.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validateLogin(array $data)
    {
        // Cargar y formatear la lista de contraseñas comunes
        $commonPasswords = config('common_passwords.common_passwords');
        $notInList = implode(',', array_map('ucfirst', array_map('strtolower', $commonPasswords)));

        return Validator::make($data, [
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                self::EMAIL_MAX_RULE,
                'exists:users,email',
                'lowercase',
            ],
            'password' => [
                'required',
                self::PASSW_REGEX_RULE,
                self::PASSW_NOTIN_RULE . $notInList,
            ],
            'remember' => 'nullable|string|in:true,false',
        ], [
            'password.not_in' => self::PASSW_MESS_NOTIN,
            'password.regex' => self::PASSW_MESS_REGEX,
            'email.regex' => ' Formatos válidos. Ej: correo@dominio.com ó correo@192.168.1.1',
        ]);
    }

    /**
     * Validar los datos del registros comunes del usuario.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data, $id = null, bool $isUpdate = false)
    {
        $commonPasswords = config('common_passwords.common_passwords');
        $notInList = implode(',', array_map('ucfirst', array_map('strtolower', $commonPasswords)));
        // Establece la regla de validación para la contraseña según si se proporciona
        $isRequiredPassword = isset($data['password']) && !empty($data['password']) ? 'required' : 'nullable';

        $rules = [
            'first_name' => [
                'required',
                'string',
                User::NAMES_LENGTH_RULE,
                'distinct',
                User::NAMES_REGEX_RULE,
            ],
            'last_name' => [
                'required',
                'string',
                User::NAMES_LENGTH_RULE,
                'distinct',
                User::NAMES_REGEX_RULE,
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                User::EMAIL_MAX_RULE,
                'unique:' . User::class . ($isUpdate ? ',email,' . $id . ',id,deleted_at,NULL' : ''),
            ],
            'password' => [
                $isRequiredPassword,
                'confirmed',
                User::PASSW_REGEX_RULE,
                User::PASSW_NOTIN_RULE . $notInList,
            ],
            'password_confirmation' => [
                $isRequiredPassword,
                'same:password',
            ],
            [
                'first_name.regex' => User::FISTN_MESS_REGEX,
                'last_name.regex' => User::FISTN_MESS_REGEX,
                'password.not_in' => User::PASSW_MESS_NOTIN,
                'password.regex' => User::PASSW_MESS_REGEX,
                'password_confirmation.same' => User::PASSC_MESS_SAME,
                'email.regex' => ' Formatos válidos. Ej: correo@dominio.com ó correo@192.168.1.1',
            ],
        ];

        return Validator::make($data, $rules);
    }

    /**
     * Validar la dirección de correo electrónico de la solicitud.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateSendEmail(array $data)
    {
        return Validator::make($data, [
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                self::EMAIL_MAX_RULE,
                'exists:users,email',
                'lowercase',
            ],
        ], [
            'email.exists' => 'El Correo Electrónico No Existe En El Sistema.',
            'email.regex' => ' Formatos válidos. Ej: correo@dominio.com ó correo@192.168.1.1',
        ]);
    }

    /**
     * Validar los datos de Nuevas contraseñas.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validateNewPassword(array $data)
    {
        // Cargar y formatear la lista de contraseñas comunes
        $commonPasswords = config('common_passwords.common_passwords');
        $notInList = implode(',', array_map('ucfirst', array_map('strtolower', $commonPasswords)));

        return Validator::make($data, [
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                self::EMAIL_MAX_RULE,
                'exists:users,email',
                'lowercase',
            ],
            'password' => [
                'required',
                'confirmed',
                self::PASSW_REGEX_RULE,
                self::PASSW_NOTIN_RULE . $notInList,
            ],
            'password_confirmation' => [
                'required',
                'same:password',
            ],
        ], [
            'password.not_in' => self::PASSW_MESS_NOTIN,
            'password.regex' => self::PASSW_MESS_REGEX,
            'email.regex' => ' Formatos válidos. Ej: correo@dominio.com ó correo@192.168.1.1',
        ]);
    }

    // CRUD Operations
    private static function handleOperation(callable $operation, array $data = [], $id = null, $validate = true)
    {
        $response = [];

        try {
            if ($validate) {
                $validator = self::validationRules($data, $id, $id ? true : false);
                if ($validator->fails()) {
                    $response = [
                        'status' => false,
                        'message' => trans(config('constants.MESSAGES.MESS_BAD_REQUEST')),
                        'errors' => $validator->errors()->toArray(),
                        'status_code' => config('constants.STATUS_CODES.BAD_REQUEST'),
                    ];
                    \Illuminate\Support\Facades\Log::error('Validation failed', ['errors' => $response['errors']]);
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
            \Illuminate\Support\Facades\Log::error(trans('Data Operation failed'), $response);
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
