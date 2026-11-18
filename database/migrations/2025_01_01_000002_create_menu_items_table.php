<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->boolean('is_available')->default(true);
            $table->string('image_url')->nullable();
            $table->unsignedSmallInteger('preparation_time')->nullable()->comment('Minutos');
            $table->timestamps();

            $table->unique(['category_id', 'name']);
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
