<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retailers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')
                ->onUpdate('cascade')->onDelete('set null');
            $table->string('name');
            $table->string('abbreviation');
            $table->string('domain', 2083);
            $table->string('ecommerce_url', 2083);
            $table->binary('logo')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('priority')->default(1);
            $table->timestamp('last_crawled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
            $table->index('abbreviation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retailers');
    }
}
