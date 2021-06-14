<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_items', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->foreignId('company_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name', 200);
            $table->string('shortcut', 100);
            $table->string('shortcut_num', 100);
            $table->foreignId('account_category_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('corresponding_income_id');
            $table->foreignId('corresponding_expense_id');
            $table->foreignId('walletable_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->char('type', 10)->nullable()
                ->comment('income or expense');
            $table->char('subtype', 1)->nullable()
                ->comment('F - Food, L - Labour, O - Other');
            $table->boolean('available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_items');
    }
}
