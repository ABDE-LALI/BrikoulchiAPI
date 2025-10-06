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
        Schema::create('initial_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->unique();
            $table->string('image')->nullable();
            $table->integer('listings');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('global_service_id')->constrained('global_services')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initial_services');
    }
};
