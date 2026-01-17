<?php

namespace App\Services\Backend;

use App\Models\WorkOrder;
use App\Services\BaseService;

class WorkOrderService extends BaseService
{
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function createWorkOrder(array $data, array $files = []): array
    {
        return $this->executeTransaction(function () use ($data, $files) {
            $workOrder = WorkOrder::create($data);

            foreach ($files as $file) {
                $upload = $this->attachmentService->upload($file, 'work-orders');
                if ($upload['status']) {
                    $workOrder->attachments()->create($upload['data']);
                }
            }

            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $workOrder);
        }, 'Work Order creation failed');
    }

    public function updateWorkOrder(string $id, array $data, array $files = []): array
    {
        return $this->executeTransaction(function () use ($id, $data, $files) {
            $workOrder = WorkOrder::find($id);
            if (!$workOrder) return $this->error(trans('Work Order not found'), [], 404);
            $workOrder->update($data);

            foreach ($files as $file) {
                $upload = $this->attachmentService->upload($file, 'work-orders');
                if ($upload['status']) {
                    $workOrder->attachments()->create($upload['data']);
                }
            }

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $workOrder);
        }, 'Work Order update failed');
    }

    public function deleteWorkOrder(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $workOrder = WorkOrder::find($id);
            if (!$workOrder) return $this->error(trans('Work Order not found'), [], 404);
            $workOrder->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Work Order deletion failed');
    }
}
