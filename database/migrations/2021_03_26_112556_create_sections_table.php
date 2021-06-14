<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->foreignId('company_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()
                ->references('id')->on('sections')
                ->onDelete('cascade');
            $table->string('name', 100);
            $table->string('long_name', 255)->nullable();
            $table->string('shortcut1', 50)->nullable();
            $table->string('shortcut2', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
