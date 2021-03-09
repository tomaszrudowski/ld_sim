<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->integer('population_id', false, true)->nullable(false);
            $table->foreign('population_id')->references('id')->on('populations')->onDelete('cascade');
            $table->smallInteger('expertise', false, true)->nullable(false);
            $table->smallInteger('confidence', false, true)->nullable(false);
            $table->smallInteger('following', false, true)->nullable(false);
            $table->char('group')->nullable(false)->default('x');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voters');
    }
}
