<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationNotificationTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_notification_tags', function (Blueprint $table) {
            $table->foreignId('notification_id')->nullable()
                ->references('id')->on('notifications')
                ->onDelete('cascade');
            $table->foreignId('notification_tag_id')->nullable()
                ->references('id')->on('notification_tags')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_notification_tags');
    }
}
