<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Menu extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'parent_id',
        'title',
        'subtitle',
        'code',
        'url',
        'model',
        'icon',
        'type',
        'show',
        'active',
        'sort',
        'maintenance',
        'coming_soon',
    ];
    protected $casts = [
        'show' => 'boolean',
        'active' => 'boolean',
        'id' => 'string',
        'parent_id' => 'string',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function parent(): object
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): object
    {
        return $this->hasMany(Menu::class, 'parent_id')->sort();
    }

    public function announcement(): object
    {
        return $this->hasMany(Announcement::class, 'menu_id')->whereDate('end', '>=', date('Y-m-d'))->orderBy('start', 'desc');
    }

    public function accessChildren(): object
    {
        return $this->hasMany(Menu::class, 'parent_id')->with(['accessChildren'])->whereHas('access_menu', function ($query) {
            $query->where('access_group_id', auth()->user()->access_group_id);
        })->show()->active()->sort();
    }

    public function scopeSort($query): object
    {
        return $query->orderBy('sort', 'asc');
    }

    public function scopeActive($query): object
    {
        return $query->where('active', true);
    }

    public function scopeShow($query): object
    {
        return $query->where('show', true);
    }

    public function access_menu(): object
    {
        return $this->hasMany(AccessMenu::class);
    }

    public function getModelAttribute(): string
    {
        return Str::replace('/', '\\', config('master.app.root.model')) . '\\' . $this->attributes['model'];
    }

    /**
     * Valida los datos de entrada para almacenar datos.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRulesStore(array $data)
    {
        return Validator::make($data, [
            'parent_id' => 'nullable',
            'title' => 'required|unique:menus',
            'subtitle' => 'nullable',
            'code' => 'required|unique:menus',
            'url' => 'required|unique:menus',
            'model' => 'nullable',
            'icon' => 'required',
            'type' => 'required',
            'show' => 'nullable',
            'active' => 'nullable',
            'access_group_id' => 'required|array|exists:access_groups,id',
        ]);
    }

    /**
     * Valida los datos de entrada para actualizar datos.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRulesUpdate(array $data, string $id)
    {
        return Validator::make($data, [
            'parent_id' => 'nullable',
            'title' => 'required',
            'subtitle' => 'nullable',
            'code' => 'required|unique:menus,code,' . $id,
            'url' => 'required|unique:menus,url,' . $id,
            'model' => 'nullable',
            'icon' => 'required',
            'type' => 'required',
            'show' => 'nullable',
            'active' => 'nullable',
        ]);
    }

    public static function deleteData($id)
    {
        $response = [];

        $dataModel = self::find($id);
        if (!$dataModel) {
            $response = [
                'status' => false,
                'message' => trans(config('constants.MESSAGES.DATA_DELETE_FAILED')) . trans(config('constants.MESSAGES.ERROR_TRYING_TO_DELETE_RESOURCE')) . ": $id",
                'status_code' => config('constants.STATUS_CODES.NOT_FOUND'),
            ];
            Log::error('Error fn(MenuModel) deleteData', [
                'detail' => trans(config('constants.MESSAGES.DATA_NOT_FOUND')),
                'error' => $response['message'],
            ]);
        } else {
            $dataModel->delete();
            $response = [
                'status' => true,
                'message' => trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')),
                'status_code' => config('constants.STATUS_CODES.OK'),
            ];
        }
        return $response;
    }
}
