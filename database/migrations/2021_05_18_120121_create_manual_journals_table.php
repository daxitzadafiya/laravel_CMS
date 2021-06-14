<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_journals', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->foreignId('company_id')
                ->constrained()
                ->onDelete('cascade');
            $table->date('issue_date')->index();
            $table->foreignId('account_item_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->BigInteger('amount')->nullable();
            $table->BigInteger('vat')->nullable();
            $table->string('description', 255)->nullable();
            $table->char('entry_side', 20)->nullable();

            $table->index(['account_item_id', 'entry_side', 'company_id', 'issue_date'], 'm_journals_index_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_journals');
    }
}
