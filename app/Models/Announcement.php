<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Clase Announcement que representa un anuncio en el sistema.
 */
class Announcement extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    // Definición de atributos que se deben convertir a tipos específicos
    protected $casts = [
        'id' => 'string',
        'menu_id' => 'string',
        'parent_id' => 'string',
        'publish' => 'boolean',
    ];

    // Atributos que son asignables en masa
    protected $fillable = [
        'id',
        'menu_id',
        'title',
        'start',
        'end',
        'content',
        'urgency',
        'publish',
        'parent_id',
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
     * Relación con el anuncio padre.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    /**
     * Relación con los anuncios hijos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Obtiene el enlace del anuncio.
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        return url(config('master.app.url.backend') . "/announcement-detail/{$this->id}/" . Str::slug(Str::replace('/', '-', $this->title)));
    }

    /**
     * Obtiene los días restantes hasta la fecha de finalización.
     *
     * @return int
     */
    public function getDaysLeftAttribute()
    {
        return Carbon::createFromDate($this->end)->diffInDays();
    }

    /**
     * Relación con el modelo File.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function file()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Relación con el modelo Notification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function notification()
    {
        return $this->morphOne(Notification::class, 'notifiable');
    }

    /**
     * Valida los datos de entrada para actualizar una FAQ.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data, bool $isUpdate = false)
    {
        return Validator::make($data, [
            'menu_id' => 'required|exists:menus,id',
            'title' => 'required|required|regex:/^[a-zA-Z0-9\s\-\.\,\(\)\'\’\“\”\/]+$/',
            'start' => 'required|date' . ($isUpdate ? '' : '|after_or_equal:today'),
            'end' => 'required|date|after_or_equal:start',
            'content' => 'required',
            'urgency' => 'required',
            'publish' => 'nullable',
            'parent_id' => 'nullable',
            'file.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',
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
