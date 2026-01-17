<?php

namespace App\Services\Backend;

use App\Models\IncidentReport;
use App\Services\BaseService;

class IncidentReportService extends BaseService
{
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function createIncident(array $data, array $files = []): array
    {
        return $this->executeTransaction(function () use ($data, $files) {
            $incident = IncidentReport::create($data);

            foreach ($files as $file) {
                $upload = $this->attachmentService->upload($file, 'incidents');
                if ($upload['status']) {
                    $incident->attachments()->create($upload['data']);
                }
            }

            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $incident);
        }, 'Incident Report creation failed');
    }

    public function updateIncident(string $id, array $data, array $files = []): array
    {
        return $this->executeTransaction(function () use ($id, $data, $files) {
            $incident = IncidentReport::find($id);
            if (!$incident) return $this->error(trans('Incident Report not found'), [], 404);
            $incident->update($data);

            foreach ($files as $file) {
                $upload = $this->attachmentService->upload($file, 'incidents');
                if ($upload['status']) {
                    $incident->attachments()->create($upload['data']);
                }
            }

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $incident);
        }, 'Incident Report update failed');
    }

    public function deleteIncident(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $incident = IncidentReport::find($id);
            if (!$incident) return $this->error(trans('Incident Report not found'), [], 404);
            $incident->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Incident Report deletion failed');
    }
}
