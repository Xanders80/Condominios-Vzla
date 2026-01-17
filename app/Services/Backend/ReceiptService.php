<?php

namespace App\Services\Backend;

use App\Models\Receipt;
use App\Services\BaseService;

class ReceiptService extends BaseService
{
    /**
     * Create a new receipt manually.
     */
    public function createReceipt(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $receipt = Receipt::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $receipt);
        }, 'Receipt creation failed');
    }

    /**
     * Update an existing receipt.
     */
    public function updateReceipt(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $receipt = Receipt::find($id);
            if (!$receipt) {
                return $this->error(trans('Receipt not found'), [], 404);
            }
            $receipt->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $receipt);
        }, 'Receipt update failed');
    }

    /**
     * Delete a receipt.
     */
    public function deleteReceipt(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $receipt = Receipt::find($id);
            if (!$receipt) {
                return $this->error(trans('Receipt not found'), [], 404);
            }
            $receipt->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Receipt deletion failed');
    }
}
