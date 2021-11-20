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
            $table->bigInteger('sayembara_id');
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
            $table->foreign('sayembara_id')->references('id')->on('sayembaras')->nullOnDelete();
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('sub_district_id')->references('id')->on('sub_districts')->nullOnDelete();
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
