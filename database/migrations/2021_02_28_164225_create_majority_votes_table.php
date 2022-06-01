<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMajorityVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('majority_votes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('election_id', false, true)->nullable(false);
            $table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade');
            $table->bigInteger('voter_id', false, true)->nullable(false);
            $table->foreign('voter_id')->references('id')->on('voters')->onDelete('cascade');
            $table->boolean('vote')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('majority_votes');
    }
}
