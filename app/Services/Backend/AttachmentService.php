<?php

namespace App\Services\Backend;

use App\Models\Attachment;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentService extends BaseService
{
    public function upload(UploadedFile $file, string $folder = 'attachments'): array
    {
        try {
            $name = $file->getClientOriginalName();
            $path = $file->store($folder, 'public');

            return [
                'status' => true,
                'data' => [
                    'file_path' => $path,
                    'file_name' => $name,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function attach(Model $model, array $attachmentData): Attachment
    {
        return $model->attachments()->create($attachmentData);
    }

    public function delete(string $id): bool
    {
        $attachment = Attachment::find($id);
        if ($attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            return $attachment->delete();
        }
        return false;
    }
}
