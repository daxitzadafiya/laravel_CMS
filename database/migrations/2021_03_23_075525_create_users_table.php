<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('last_name_kana', 50)->nullable();
            $table->string('first_name_kana', 50)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('company_id')->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->string('position', 50)->nullable();
            $table->char('role', 2)->default('U')
                ->comment('SA - super admin, A - admin, U - user');
            $table->string('photo', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index([
                'email',
                'password',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
