<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompletionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('completions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('activity_hash');
            $table->integer('character_id')->unsigned();
            $table->boolean('completed')->default(false);
            $table->float('assists');
            $table->float('deaths');
            $table->float('kills');
            $table->float('kills_deaths_ratio');
            $table->float('kills_deaths_assists');
            $table->float('activity_duration_seconds');
            $table->float('player_count');
            $table->timestamp('period');
            $table->timestamps();

            $table->foreign('character_id')->references('id')->on('characters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('completions');
    }
}
