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
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['attribute', 'subtotal', 'user_type']);
            $table->string('condition'); // e.g. attribute_option_id, 100, or "company"
            $table->decimal('value', 10, 2); // the discount amount
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_rules');
    }
};
