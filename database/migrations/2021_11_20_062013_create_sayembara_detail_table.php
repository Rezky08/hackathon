<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSayembaraDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sayembara_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('city_id');
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('sub_district_id')->nullable();
            $table->string('title');
            $table->bigInteger('thumbnail')->nullable();
            $table->string('present_type');
            $table->string('present_value')->nullable();
            $table->string('category');
            $table->integer('max_participant')->default(10);
            $table->integer('max_winner')->default(1);
            $table->longText('content');
            $table->json('limit')->nullable();
            $table->timestamps();
            $table->foreign('thumbnail')->references('id')->on('attachments')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sayembara_details');
    }
}
