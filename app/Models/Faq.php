<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Faq extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'title',
        'menu_id',
        'parent_id',
        'description',
        'visitors',
        'like',
        'dislike',
        'publish',
    ];

    protected $casts = [
        'publish' => 'boolean',
    ];

    /**
     * Relación con el modelo Menu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    /**
     * Relación con el modelo Faq para obtener el padre.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Faq::class, 'parent_id');
    }

    /**
     * Relación con el modelo Faq para obtener los hijos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Faq::class, 'parent_id');
    }

    /**
     * Relación para obtener la familia de FAQs por menú.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function family()
    {
        return $this->hasMany(Faq::class, 'menu_id');
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

    /**
     * Relación polimórfica para múltiples archivos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Relación polimórfica para un log.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function log()
    {
        return $this->morphOne(Log::class, 'loggable');
    }

    /**
     * Obtiene la carpeta donde se almacenarán los archivos.
     *
     * @return string
     */
    public function getFolderAttribute()
    {
        return Str::lower(Str::snake(class_basename($this), '-')) . '/' . now()->format('Y/m/d');
    }

    /**
     * Valida los datos de entrada para almacenar una nueva FAQ.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRulesStore(array $data)
    {
        return Validator::make($data, [
            'title' => [
                'required',
                'string',
            ],
            'description' => [
                'required',
                'string',
            ],
            'file' => [
                'nullable',
                'file',
            ],
            'visitors' => [
                'required',
                'integer',
            ],
            'like' => [
                'required',
                'integer',
            ],
            'dislike' => [
                'required',
                'integer',
            ],
            'publish' => [
                'nullable',
            ],
            'menu_id' => [
                'required',
                'string',
            ],
        ]);
    }

    /**
     * Valida los datos de entrada para actualizar una FAQ.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRulesUpdate(array $data)
    {
        return Validator::make($data, [
            'title' => [
                'required',
                'string',
            ],
            'description' => [
                'required',
                'string',
            ],
            'visitors' => [
                'required',
                'integer',
            ],
            'like' => [
                'required',
                'integer',
            ],
            'dislike' => [
                'required',
                'integer',
            ],
            'menu_id' => [
                'required',
                'string',
            ],
        ]);
    }

    /**
     * Valida los datos de entrada para almacenar una nueva FAQ.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data, bool $isUpdate = false)
    {
        return Validator::make($data, [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'file' => $isUpdate ? [] : ['nullable', 'file'],
            'visitors' => ['required', 'integer'],
            'like' => ['required', 'integer'],
            'dislike' => ['required', 'integer'],
            'publish' => ['nullable'],
            'menu_id' => ['required', 'string'],
        ]);
    }

    // CRUD Operations
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
