<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_datetime')->nullable();
            $table->string('code_pay')->nullable();
            $table->string('quantity')->default("1")->nullable();
            $table->string('status_pay')->default('Pendiente')->nullable();
            $table->string('status_entry')->default('Pendiente')->nullable();
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            $table->foreignId('environment_id')->nullable()->unsigned()->constrained('environments');
            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');
            $table->softDeletes();
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
        Schema::dropIfExists('events');
    }
};
