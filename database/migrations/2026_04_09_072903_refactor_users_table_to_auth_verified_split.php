<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Since old data is not important, we just drop and recreate
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('kyc_requests'); // Dropping dependent tables first
        Schema::dropIfExists('users');
        Schema::dropIfExists('auth_users'); // Just in case it existed from failed run

        Schema::enableForeignKeyConstraints();

        // 1. Create auth_users (Login Table)
        Schema::create('auth_users', function (Blueprint $table) {
            $table->id();
            $table->string('mobile')->unique();
            $table->string('otp')->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->string('password')->nullable(); // For admin/fallback
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Create verified_users (KYC Table)
        Schema::create('verified_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auth_user_id')->constrained('auth_users')->onDelete('cascade');

            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('aadhaar_number')->nullable();

            $table->string('pan_image')->nullable();
            $table->string('aadhaar_image')->nullable();
            $table->string('selfie_image')->nullable();

            $table->enum('kyc_status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->decimal('gold_limit', 16, 4)->default(0);
            $table->decimal('silver_limit', 16, 4)->default(0);

            $table->boolean('is_trading_enabled')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verified_users');
        Schema::dropIfExists('auth_users');
    }
};
