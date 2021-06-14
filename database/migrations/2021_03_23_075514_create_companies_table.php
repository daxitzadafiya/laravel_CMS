<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->string('corporate_number', 50)->nullable();
            $table->string('name', 150)->nullable();
            $table->string('display_name', 150)->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->foreignId('prefecture_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->string('city', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('email', 255)->nullable();
            $table->char('type', 15)->default('corporate');
            $table->string('role', 50)->nullable();
            $table->integer('business_year_start_month')->nullable();
            $table->integer('business_year_start_day')->nullable();
            $table->date('registration_date')->nullable();
            $table->foreignId('industry_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('head_count_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->timestamp('connected_at')->nullable();
            $table->bigInteger('current_month_logins')->index()
                ->default(0);
            $table->bigInteger('previous_month_logins')->index()
                ->default(0);
            $table->timestamp('last_login_at')->index()
                ->nullable();
            $table->date('deals_updated_date')->nullable();
            $table->date('txns_updated_date')->nullable();
            $table->boolean('freee_syncing')->default(0);
            $table->tinyInteger('status')->index()
                ->default(0)
                ->comment('0 - Not connected, 1 - Connected');
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
        Schema::dropIfExists('companies');
    }
}
