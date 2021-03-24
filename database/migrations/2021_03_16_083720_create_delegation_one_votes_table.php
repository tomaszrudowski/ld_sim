<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegationOneVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegation_one_votes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('election_id', false, true)->nullable(false);
            $table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade');
            $table->bigInteger('voter_id', false, true)->nullable(false);
            $table->foreign('voter_id')->references('id')->on('voters')->onDelete('cascade');
            $table->boolean('vote_direct')->nullable(true);
            $table->bigInteger('vote_delegation', false, true)->nullable(true);
            $table->foreign('vote_delegation')->references('id')->on('delegation_one_votes')->onDelete('cascade');
            $table->integer('vote_weight', false, true)->nullable(false)->default(1);
            $table->char('voter_mark', 1)->nullable(false)->default('i');
            $table->boolean('vote_final')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delegation_one_votes');
    }
}
