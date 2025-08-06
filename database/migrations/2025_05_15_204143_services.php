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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->unique();
            $table->string('image')->nullable();
            $table->integer('listings');
            $table->enum('workDays', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])->default('Monday   ');
            $table->integer('workHours')->default(8);
            $table->enum('status', ['busy', 'available'])->default('available');
            $table->enum('type', ['timecount', 'freelance']);
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('address');
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->float('rating')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
