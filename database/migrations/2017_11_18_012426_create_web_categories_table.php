<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retailer_id')->unsigned()->nullable();
            $table->foreign('retailer_id')->references('id')->on('retailers')
                ->onUpdate('cascade')->onDelete('set null');
            $table->integer('web_category_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('field')->nullable();
            $table->string('url', 2083)->nullable();
            $table->tinyInteger('active')->default(1);
            $table->integer('last_crawled_products_count')->nullable();
            $table->timestamp('last_crawled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['retailer_id', 'web_category_id', 'name']);
            $table->index('name');
            $table->index(['retailer_id', 'name']);
            $table->index('slug');
            $table->index(['retailer_id', 'slug']);
        });

        Schema::table('web_categories', function (Blueprint $table) {
            $table->foreign('web_category_id')->references('id')->on('web_categories')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_categories');
    }
}
