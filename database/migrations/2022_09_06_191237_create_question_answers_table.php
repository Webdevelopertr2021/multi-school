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
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->integer("student_id");
            $table->integer("exam_id");
            $table->integer("question_id");
            $table->string("answer")->nullable();
            $table->string("correct_ans");
            $table->string("is_correct")->nullable();
            $table->integer("total_tries")->default(0);
            $table->integer("total_time_to_ans")->default(0);
            $table->string("status")->default("not_submited");
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
        Schema::dropIfExists('question_answers');
    }
};
