<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('asset');
            $table->string('side')->default('buy');
            $table->string('order_type')->default('market');
            $table->decimal('quantity', 12, 2);
            $table->decimal('price', 12, 2);
            $table->decimal('total', 14, 2);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('placed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
