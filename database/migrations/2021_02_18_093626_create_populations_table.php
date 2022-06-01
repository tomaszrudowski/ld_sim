<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('populations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('parent_id', false, true)->nullable(true)->default(null);
            $table->char('stage',1)->nullable(true)->default(null);
            $table->char('election_type')->nullable(true)->default(null);
            $table->tinyInteger('follower_factor', false, true)->nullable(false)->default(100);
            $table->tinyInteger('forgetting_factor', false, true)->nullable(false)->default(1);
            $table->timestamps();
        });

        Schema::table('populations', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('populations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('populations');
    }
}
