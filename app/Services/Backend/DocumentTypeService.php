<?php

namespace App\Services\Backend;

use App\Models\DocumentType;
use App\Services\BaseService;

class DocumentTypeService extends BaseService
{
    /**
     * Create a new document type.
     */
    public function createDocumentType(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $documentType = DocumentType::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $documentType);
        }, 'Document type creation failed');
    }

    /**
     * Update an existing document type.
     */
    public function updateDocumentType(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $documentType = DocumentType::find($id);
            if (!$documentType) {
                return $this->error(trans('Document type not found'), [], 404);
            }
            $documentType->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $documentType);
        }, 'Document type update failed');
    }

    /**
     * Delete a document type.
     */
    public function deleteDocumentType(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $documentType = DocumentType::find($id);
            if (!$documentType) {
                return $this->error(trans('Document type not found'), [], 404);
            }
            $documentType->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Document type deletion failed');
    }
}
