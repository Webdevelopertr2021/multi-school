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
        Schema::create('leave_request_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("leave_requestId");
            $table->foreign("leave_requestId")->on("leave_requests")->references("id")->onDelete("cascade");
            $table->integer("supervisor_id");
            $table->integer("teacher_id");
            $table->string("status")->default("pending");
            $table->string("rejection_reason")->nullable();
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
        Schema::dropIfExists('leave_request_approvals');
    }
};
