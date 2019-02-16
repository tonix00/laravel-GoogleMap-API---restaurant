<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCacheDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('formatted_address');
            $table->string('name',150);
            $table->string('lat',40);
            $table->string('lng',40);
            $table->string('cachename',120);
            $table->string('place_id',200);
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
        Schema::dropIfExists('cache_details');
    }
}
