<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns to leaders table
        Schema::table('leaders', function (Blueprint $table) {
            $table->string('role', 255)->nullable()->after('display_name');
            $table->text('bio')->nullable()->after('role');
            $table->string('phone', 50)->nullable()->after('bio');
            $table->string('photo_path', 255)->nullable()->after('phone');
        });

        // Create home_contacts table
        Schema::create('home_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leader_id')->constrained('leaders')->cascadeOnDelete();
            $table->date('date');
            $table->string('phone', 50);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Seed branding settings
        DB::table('settings')->insertOrIgnore([
            ['key' => 'brand_color', 'value' => '#7413dc', 'updated_at' => now()],
            ['key' => 'brand_name', 'value' => 'Scouts', 'updated_at' => now()],
            ['key' => 'header_image_path', 'value' => '', 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_contacts');

        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn(['role', 'bio', 'phone', 'photo_path']);
        });

        DB::table('settings')->whereIn('key', ['brand_color', 'brand_name', 'header_image_path'])->delete();
    }
};
