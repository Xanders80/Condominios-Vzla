<?php

namespace App\Console\Commands\Passwords;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UpdateCommonListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passwords:update-common-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update the blacklist of common passwords from remote sources.';

    /**
     * Source URL for the common passwords list.
     */
    protected const SOURCE_URL = 'https://raw.githubusercontent.com/danielmiessler/SecLists/master/Passwords/Common-Credentials/top-20-common-passwords.txt';

    /**
     * Local storage path.
     */
    protected const STORAGE_PATH = 'security/common_passwords.json';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Fetching common passwords from remote source...');

        try {
            // Ensure directory exists
            $directory = dirname(self::STORAGE_PATH);
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            $response = null;
            try {
                $response = Http::timeout(10)->get(self::SOURCE_URL);
            } catch (\Exception $e) {
                $this->warn('Network request failed: ' . $e->getMessage());
            }

            if ($response && $response->successful()) {
                $passwords = $this->sanitizePasswords($response->body());
                $this->info('Fetched ' . count($passwords) . ' passwords from remote source.');
            } else {
                $this->warn('Failed to fetch from remote source. Using fallback from config...');
                $passwords = config('common_passwords.common_passwords', []);

                if (empty($passwords)) {
                    $this->error('No fallback passwords found in config.');
                    return Command::FAILURE;
                }
            }

            if (empty($passwords)) {
                $this->error('The password list is empty.');
                return Command::FAILURE;
            }

            Storage::put(self::STORAGE_PATH, json_encode($passwords, JSON_PRETTY_PRINT));

            $this->info('Common passwords list updated successfully. Total items: ' . count($passwords));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Error updating common passwords list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Sanitize and format the raw password list.
     */
    private function sanitizePasswords(string $rawContent): array
    {
        // Split by lines, trim whitespace, remove empty values, and deduplicate
        $lines = explode("\n", $rawContent);

        $sanitized = array_filter(array_map('trim', $lines));
        $sanitized = array_unique(array_map('strtolower', $sanitized));

        return array_values($sanitized);
    }
}
