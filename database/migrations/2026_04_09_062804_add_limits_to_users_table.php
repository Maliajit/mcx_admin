<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'gold_limit')) {
                $table->decimal('gold_limit', 12, 4)->default(0)->after('can_trade');
            }
            if (!Schema::hasColumn('users', 'silver_limit')) {
                $table->decimal('silver_limit', 12, 4)->default(0)->after('gold_limit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'gold_limit')) {
                $table->dropColumn('gold_limit');
            }
            if (Schema::hasColumn('users', 'silver_limit')) {
                $table->dropColumn('silver_limit');
            }
        });
    }
};
