<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtensionDelegationElectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension_delegation_elections', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('election_id', false, true)->nullable(false);
            $table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade');
            $table->integer('initial_correct', false, true)->nullable(false)->default(0);
            $table->integer('initial_incorrect', false, true)->nullable(false)->default(0);
            $table->integer('as_delegate', false, true)->nullable(false)->default(0);
            $table->integer('as_follower', false, true)->nullable(false)->default(0);
            $table->integer('as_independent', false, true)->nullable(false)->default(0);
            $table->integer('weight_a', false, true)->nullable(false)->default(0);
            $table->integer('weight_b', false, true)->nullable(false)->default(0);
            $table->integer('reputation_a', false, false)->nullable(false)->default(0);
            $table->integer('reputation_b', false, false)->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extension_delegation_elections');
    }
}
