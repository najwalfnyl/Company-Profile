<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id('id_project');
            $table->string('name_project');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('perusahaan_id');
            $table->string('picture')->nullable();
            $table->text('description1')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('picture01')->nullable();
            $table->string('picture02')->nullable();
            $table->string('picture03')->nullable();
            $table->string('picture04')->nullable();
            $table->text('description2')->nullable();
            $table->text('description3')->nullable();
  
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('id_category')->on('project_categories')->onDelete('cascade');
            $table->foreign('perusahaan_id')->references('id')->on('perusahaans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
