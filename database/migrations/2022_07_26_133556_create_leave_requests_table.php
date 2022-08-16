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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("teacher_id");
            $table->foreign("teacher_id")->on("users")->references("id")->onDelete("cascade");
            $table->date("from_date");
            $table->date("to_date");
            $table->integer("total_days");
            $table->string("subject");
            $table->string("vacation_type");
            $table->text("desc");
            $table->integer("approved_by_manager")->default(0);
            $table->string("manager_msg")->nullable();
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
        Schema::dropIfExists('leave_requests');
    }
};
