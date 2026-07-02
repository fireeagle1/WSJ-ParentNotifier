<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'signup_enabled'],
            ['value' => '0']
        );

        Setting::updateOrCreate(
            ['key' => 'portal_name'],
            ['value' => 'Expedition Updates']
        );
    }
}
