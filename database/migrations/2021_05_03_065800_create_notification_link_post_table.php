<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationLinkPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_link_posts', function (Blueprint $table) {
            $table->id();
            $table->date('post_date');
            $table->string('title');
            $table->string('url');
            $table->string('publisher');
            $table->char('status', 1)->nullable()
                ->comment('0 - Private, 1 - Publish');
            $table->integer('clicks')->default('0');
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
        Schema::dropIfExists('notification_link_posts');
    }
}
