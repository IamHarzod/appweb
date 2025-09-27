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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stockQuantity');
            $table->unsignedBigInteger('category_id');
            $table->decimal('discountPercent', 5, 2)->default(0);
            $table->string('imageURL')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->string('line')->nullable();
            $table->string('style')->nullable();
            $table->string('status')->nullable();
    

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('products');
    }
};
