<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->foreignId('company_id')
                ->constrained()
                ->onDelete('cascade');
            $table->date('issue_date')->index();
            $table->char('type', 10)->index();
            $table->foreignId('account_item_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->BigInteger('amount')->nullable();
            $table->BigInteger('vat')->nullable();
            $table->string('description', 255)->nullable();
            $table->char('entry_side', 20)->nullable();

            $table->index(['account_item_id', 'entry_side', 'company_id', 'issue_date'], 'deals_index_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
