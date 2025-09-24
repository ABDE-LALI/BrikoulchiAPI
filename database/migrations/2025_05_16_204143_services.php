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
            $table->string('title', 20);
            $table->text('description');
            $table->string('image')->nullable();
            $table->integer('listings')->default(0);
            $table->string('workDays')->default('Mo');
            $table->string('workHours');
            $table->enum('status', ['busy', 'available'])->default('available');
            $table->enum('type', ['timecount', 'freelance', 'fulltime', 'parttime']);
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('global_service_id')->references('id')->on('global_services')->onDelete('cascade');
            $table->foreignId('initial_service_id')->references('id')->on('initial_services')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
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
