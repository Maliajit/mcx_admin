<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('pan_image_path')->nullable()->after('pan_number');
            $table->string('aadhaar_front_image_path')->nullable()->after('aadhaar_number');
            $table->string('aadhaar_back_image_path')->nullable()->after('aadhaar_front_image_path');
            $table->string('selfie_image_path')->nullable()->after('aadhaar_back_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'pan_image_path',
                'aadhaar_front_image_path',
                'aadhaar_back_image_path',
                'selfie_image_path',
            ]);
        });
    }
};
