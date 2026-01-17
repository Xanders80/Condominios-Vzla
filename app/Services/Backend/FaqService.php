<?php

namespace App\Services\Backend;

use App\Models\Faq;
use App\Services\BaseService;
use App\Support\Helper;
use Illuminate\Support\Facades\Storage;

class FaqService extends BaseService
{
    /**
     * Create a new FAQ.
     */
    public function createFaq(array $data, string $description, $file = null): array
    {
        return $this->executeTransaction(function () use ($data, $description, $file) {
            $faq = Faq::create($data);
            if (!$faq) {
                return $this->error(trans(config('constants.MESSAGES.DATA_FAILED')));
            }

            // Handle description with Base64 images
            $faq->update(['description' => Helper::uploadImageBase64($description, $faq)]);

            // Handle file upload
            if ($file) {
                $faq->file()->create([
                    'data' => [
                        'disk' => config('filesystems.default'),
                        'target' => Storage::putFile($faq->folder, $file),
                        'name' => $file->getClientOriginalName(),
                    ],
                ]);
            }

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $faq);
        }, 'Faq creation failed');
    }

    /**
     * Update an existing FAQ.
     */
    public function updateFaq(string $id, array $data, string $description, $file = null): array
    {
        return $this->executeTransaction(function () use ($id, $data, $description, $file) {
            $faq = Faq::find($id);
            if (!$faq) {
                return $this->error(trans('Faq not found'), [], 404);
            }

            $data['publish'] = isset($data['publish']) ? 1 : 0;
            $data['description'] = Helper::uploadImageBase64($description, $faq);

            $faq->update($data);

            // Handle file upload
            if ($file) {
                $faq->file?->forceDelete();
                $faq->file()->create([
                    'data' => [
                        'disk' => config('filesystems.default'),
                        'target' => Storage::putFile($faq->folder, $file),
                        'name' => $file->getClientOriginalName(),
                    ],
                ]);
            }

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $faq);
        }, 'Faq update failed');
    }

    /**
     * Delete an FAQ.
     */
    public function deleteFaq(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $faq = Faq::find($id);
            if (!$faq) {
                return $this->error(trans('Faq not found'), [], 404);
            }
            $faq->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Faq deletion failed');
    }
}
