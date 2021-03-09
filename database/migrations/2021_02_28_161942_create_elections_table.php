<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->integer('population_id', false, true)->nullable(false);
            $table->foreign('population_id')->references('id')->on('populations')->onDelete('cascade');
            $table->char('type');
            $table->integer('total_correct', false, true)->nullable(false)->default(0);
            $table->integer('total_incorrect', false, true)->nullable(false)->default(0);
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
        Schema::dropIfExists('elections');
    }
}
