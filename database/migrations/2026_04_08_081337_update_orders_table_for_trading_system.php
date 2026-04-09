<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'type')) {
                $table->enum('type', ['market', 'limit'])->default('market')->after('asset');
            }

            if (!Schema::hasColumn('orders', 'target_price')) {
                $table->decimal('target_price', 12, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['waiting', 'pending', 'approved', 'rejected'])->default('pending')->after('total');
            }

            if (!Schema::hasColumn('orders', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            }

            if (!Schema::hasColumn('orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('orders', 'target_price')) {
                $table->dropColumn('target_price');
            }
            if (Schema::hasColumn('orders', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('orders', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            // Note: status column will remain, but enum values changed - handle manually if needed
        });
    }
};
