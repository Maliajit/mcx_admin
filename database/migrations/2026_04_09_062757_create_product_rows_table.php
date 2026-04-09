<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_rows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['gold', 'silver']);
            $table->decimal('margin', 12, 2)->default(0);
            $table->decimal('adjustment', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_rows');
    }
};
