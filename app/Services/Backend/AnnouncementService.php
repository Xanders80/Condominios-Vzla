<?php

namespace App\Services\Backend;

use App\Models\Announcement;
use App\Services\BaseService;
use App\Support\Helper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AnnouncementService extends BaseService
{
    /**
     * Create a new announcement.
     */
    public function createAnnouncement(array $data, array $files = [], $user = null): array
    {
        return $this->executeTransaction(function () use ($data, $files, $user) {
            // Pre-process data
            $data['content'] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $data['content'] ?? '');
            $data['publish'] = isset($data['publish']) ? 1 : 0;

            $announcement = Announcement::create($data);

            if ($announcement) {
                // Handle files
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $announcement->file()->create([
                            'data' => [
                                'name' => $file->getClientOriginalName(),
                                'disk' => config('filesystems.default'),
                                'target' => Storage::disk(config('filesystems.default'))->putFile($announcement->menu->code . '/' . date('Y') . '/' . date('m') . '/' . date('d'), $file),
                            ],
                        ]);
                    }
                }

                // Handle notifications
                if ($user && isset($user->all_user_id)) {
                    Helper::sendNotification($announcement, $user->all_user_id, [
                        'title' => trans('New Announcement'),
                        'link' => $announcement->link,
                        'icon' => 'fa fa-bullhorn',
                        'color' => 'text-info',
                        'content' => $announcement->title,
                    ]);
                }

                return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $announcement);
            }

            return $this->error(trans(config('constants.MESSAGES.DATA_FAILED')));
        }, 'Announcement creation failed');
    }

    /**
     * Update an existing announcement.
     */
    public function updateAnnouncement(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $announcement = Announcement::find($id);
            if (!$announcement) {
                return $this->error(trans('Announcement not found'), [], 404);
            }

            // Pre-process data
            $data['content'] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $data['content'] ?? '');
            $data['publish'] = isset($data['publish']) ? 1 : 0;

            $announcement->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $announcement);
        }, 'Announcement update failed');
    }

    /**
     * Delete an announcement.
     */
    public function deleteAnnouncement(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $announcement = Announcement::find($id);
            if (!$announcement) {
                return $this->error(trans('Announcement not found'), [], 404);
            }
            $announcement->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Announcement deletion failed');
    }
}
