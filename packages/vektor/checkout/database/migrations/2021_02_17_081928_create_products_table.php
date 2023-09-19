<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vektor\Checkout\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained(table: 'products')->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('name_label');
            $table->string('sku')->nullable();
            $table->decimal('price');
            $table->json('images')->nullable();
            $table->json('configuration')->nullable();
            $table->json('attributes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};