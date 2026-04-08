<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone')->nullable()->after('email');
            $table->string('gst_number')->nullable()->after('phone');
            $table->string('pan_number')->nullable()->after('gst_number');
            $table->string('aadhaar_number')->nullable()->after('pan_number');
            $table->string('selfie_reference')->nullable()->after('aadhaar_number');
            $table->string('kyc_status')->default('unverified')->after('selfie_reference');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_status');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'phone',
                'gst_number',
                'pan_number',
                'aadhaar_number',
                'selfie_reference',
                'kyc_status',
                'kyc_submitted_at',
                'kyc_verified_at',
            ]);
        });
    }
};
