<?php

namespace App\Services\Backend;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    /**
     * Create a new user.
     */
    public function createUser(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $user = User::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $user);
        }, 'User creation failed');
    }

    /**
     * Update an existing user.
     */
    public function updateUser(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $user = User::find($id);
            if (!$user) {
                return $this->error(trans(config('constants.MESSAGES.USER_NOT_FOUND')), [], 404);
            }
            $user->update($data);
            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $user);
        }, 'User update failed');
    }

    /**
     * Delete a user.
     */
    public function deleteUser(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $user = User::find($id);
            if (!$user) {
                return $this->error(trans(config('constants.MESSAGES.USER_NOT_FOUND')), [], 404);
            }
            $user->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'User deletion failed');
    }

    /**
     * Calculate user statistics for the dashboard.
     */
    public function getUserStatistics(): array
    {
        $statistics = User::withUserStatistics()->first();
        $totalUsers = (int) $statistics->total_users;
        $verifiedUsers = (int) $statistics->verified_users;
        $pendingVerification = (int) $statistics->pending_verification;

        return [
            'totalUsers' => $totalUsers,
            'verifiedUsers' => $verifiedUsers,
            'pendingVerification' => $pendingVerification,
            'verifiedPercentage' => $this->calculatePercentage($verifiedUsers, $totalUsers),
            'pendingPercentage' => $this->calculatePercentage($pendingVerification, $totalUsers),
        ];
    }

    /**
     * Calculate percentage helper.
     */
    private function calculatePercentage(int $part, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }
        return round(($part / $total) * 100) . '%';
    }
}
