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
        Schema::create('assigned_supervisors', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("teacher_id");
            $table->foreign("teacher_id")->references("id")->on("users")->onDelete("cascade");

            $table->integer("supervisor_id");

            
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
        Schema::dropIfExists('assigned_supervisors');
    }
};
