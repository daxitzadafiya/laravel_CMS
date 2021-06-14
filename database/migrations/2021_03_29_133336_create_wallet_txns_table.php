<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTxnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_txns', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->foreignId('company_id')
                ->constrained()
                ->onDelete('cascade');
            $table->date('date')->nullable();
            $table->foreignId('walletable_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->char('entry_side', 20);
            $table->unsignedBigInteger('amount')->nullable();
            $table->unsignedBigInteger('balance')->nullable();
            $table->unsignedBigInteger('due_amount')->nullable();
            $table->string('description', 255)->nullable();
            $table->tinyInteger('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_txns');
    }
}
