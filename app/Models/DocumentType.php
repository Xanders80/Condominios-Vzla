<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DocumentType extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = ['id', 'name'];
    protected $casts = [];
    protected $table = 'document_types';

    /**
     * Common validation rules for Condominios.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validationRules(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|unique:' . DocumentType::class,
        ]);
    }

    // CRUD Operations
    public static function createData(array $data)
    {
        $response = [];

        $validator = self::validationRules($data);
        if ($validator->fails()) {
            // Devolver una respuesta JSON con los errores de validación
            $response = [
                'status' => false,
                'message' => trans(config('constants.MESSAGES.MESS_BAD_REQUEST')),
                'errors' => $validator->errors()->toArray(),
                'status_code' => config('constants.STATUS_CODES.BAD_REQUEST'),
            ];
            Log::error('Error fn(DocumentTypeModel) createData', [
                'detail' => $response['message'],
                'error' => trans('Validation failed while creating data.'),
                'data' => 'Data: ' . json_encode($response['errors']),
            ]);
        } else {
            self::create($data);
            $response = [
                'status' => true,
                'message' => trans(config('constants.MESSAGES.MESS_CREATED')),
                'status_code' => config('constants.STATUS_CODES.OK'),
            ];
        }

        return $response;
    }

    public static function updateData($id, array $data)
    {
        $response = [];

        $dataModel = self::find($id);
        if (!$dataModel) {
            $response = [
                'status' => false,
                'message' => trans(config('constants.MESSAGES.DATA_UPDATE_FAILED')) . trans(config('constants.MESSAGES.ERROR_TRYING_TO_UPDATE_RESOURCE')) . ": $id",
                'status_code' => config('constants.STATUS_CODES.NOT_FOUND'),
            ];
            Log::error('Error fn(DocumentTypeModel) updateData', [
                'detail' => trans(config('constants.MESSAGES.DATA_NOT_FOUND')),
                'error' => $response['message'],
            ]);
        } else {
            $validator = self::validationRules($data, $id);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'message' => trans(config('constants.MESSAGES.MESS_BAD_REQUEST')),
                    'errors' => $validator->errors()->toArray(),
                    'status_code' => config('constants.STATUS_CODES.BAD_REQUEST'),
                ];
                Log::error('Error fn(DocumentTypeModel) updateData', [
                    'detail' => $response['message'],
                    'error' => trans('Validation failed while updating data.'),
                    'data' => 'Data: ' . json_encode($response['errors']),
                ]);
            } else {
                $dataModel->update($data);
                $response = [
                    'status' => true,
                    'message' => trans(config('constants.MESSAGES.DATA_SUCCESS')),
                    'status_code' => config('constants.STATUS_CODES.OK'),
                ];
            }
        }

        return $response;
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
            Log::error('Error fn(DocumentTypeModel) deleteData', [
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
