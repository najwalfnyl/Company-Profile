<?php

// database/migrations/2024_10_14_000000_create_galleries_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('image_activity1')->nullable();
            $table->string('image_activity2')->nullable();
            $table->string('image_activity3')->nullable();
            $table->string('image_activity4')->nullable();
            $table->string('image_activity5')->nullable();
            $table->string('image_activity6')->nullable();
            $table->string('image_activity7')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
}
