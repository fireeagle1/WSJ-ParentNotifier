<?php

namespace Database\Seeders;

use App\Models\Leader;
use Illuminate\Database\Seeder;

class AdminLeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates the first admin leader account using configurable
     * environment variables with sensible defaults.
     */
    public function run(): void
    {
        Leader::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@parentnotifier.local')],
            [
                'display_name' => env('ADMIN_NAME', 'Admin Leader'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'is_admin' => true,
            ]
        );
    }
}
