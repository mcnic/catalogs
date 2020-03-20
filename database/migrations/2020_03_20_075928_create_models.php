<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('models', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('firm', 50)->index();
            $table->string('title', 100)->uniqid();
            $table->string('url', 100)->default('')->index();
            $table->string('group', 50)->index();
            $table->string('start', 4)->default('');
            $table->string('end', 4)->default('');
            $table->string('img', 300)->default('');
            $table->string('img_local', 300)->default('');
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
        Schema::dropIfExists('models');
    }
}
