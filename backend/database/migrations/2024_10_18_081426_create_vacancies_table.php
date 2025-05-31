<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacanciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->integer('age');
            $table->unsignedBigInteger('career_id');
            $table->string('CV')->nullable(); // Path to the uploaded CV PDF
            $table->string('Portofolio')->nullable(); // Path to the uploaded Portofolio PDF
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('career_id')
                  ->references('id')
                  ->on('careers')
                  ->onDelete('cascade'); // Delete vacancies when the related career is deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacancies');
    }
}
