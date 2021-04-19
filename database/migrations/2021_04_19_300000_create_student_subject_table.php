<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * EXAMPLE::PROGETTO MANY TO MANY MOLTI A MOLTI
 */
class CreateStudentSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_subject', function (Blueprint $table) {
            $table->id();
            $table->integer('score');
            //data dell'interrogazione
            $table->date('date')->nullable();
            //chiave esterna student_id che si riferisce al campo id nella tabella students
            //se viene cancellato lo studente elimina i voti associati
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            //in questo caso la coppia non è unique perché uno studente ha più voti in più materie
//            $table->unique(['student_id', 'subject_id']);
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
        Schema::dropIfExists('student_subject');
    }
}
