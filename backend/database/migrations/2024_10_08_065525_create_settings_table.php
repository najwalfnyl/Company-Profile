<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); // Menambahkan kolom id otomatis
            $table->string('description1');
            $table->string('description2');
            $table->string('background_header');
            $table->string('background_footer');
            $table->integer('year');
            $table->string('email');
            $table->string('phone');
            $table->string('facebook');
            $table->string('instagram');
            $table->string('linkedln');
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
