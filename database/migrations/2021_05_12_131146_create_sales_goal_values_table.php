<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesGoalValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_goal_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_goal_id')
                ->constrained()
                ->onDelete('cascade');
            $table->year('year')->index();
            $table->integer('month');
            $table->unsignedBigInteger('goal');
            $table->timestamps();

            $table->unique(['sales_goal_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_goal_values');
    }
}
