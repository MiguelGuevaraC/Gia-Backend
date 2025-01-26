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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('correlative')->nullable();
            $table->string('name')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->string('comment')->nullable();

            $table->string('status')->default('Próximo')->nullable();
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
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
