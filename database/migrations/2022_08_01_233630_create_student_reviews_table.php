<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("student_id");
            $table->foreign("student_id")->references("id")->on("students")->onDelete("cascade");

            $table->integer("teacher_id");
            $table->integer("rate1");
            $table->integer("rate2");
            $table->integer("rate3");
            $table->integer("rate4");
            $table->integer("rate5");
            $table->integer("rate6");
            $table->float("total");
            $table->text("feedback")->nullable();
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
        Schema::dropIfExists('student_reviews');
    }
};
