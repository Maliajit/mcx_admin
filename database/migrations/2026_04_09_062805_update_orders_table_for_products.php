<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'product_type')) {
                $table->enum('product_type', ['row', 'coin'])->nullable()->after('asset');
            }
            if (!Schema::hasColumn('orders', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('product_type');
            }
            if (!Schema::hasColumn('orders', 'quantity')) {
                $table->decimal('quantity', 12, 4)->default(1)->after('product_id'); // Weight in grams or count
            }
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0)->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'product_type')) {
                $table->dropColumn('product_type');
            }
            if (Schema::hasColumn('orders', 'product_id')) {
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('orders', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('orders', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
        });
    }
};
