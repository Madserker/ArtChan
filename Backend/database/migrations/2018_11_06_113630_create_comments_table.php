<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawComments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('text');

            $table->integer('username')->unsigned()->index();
            $table->foreign('username')->references('username')->on('users');

            $table->integer('draw_id')->unsigned()->index();
            $table->foreign('draw_id')->references('id')->on('draws');
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
        Schema::dropIfExists('comments');
    }
}
