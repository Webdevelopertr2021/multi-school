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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer("teacher_id");
            $table->date("paid_month");
            $table->string("bank_name")->nullable();
            $table->string("reciept_number");
            $table->integer("amount");
            $table->string("note")->nullable();
            $table->string("status")->default("pending");
            $table->string("attachments")->nullable();
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
        Schema::dropIfExists('payments');
    }
};
