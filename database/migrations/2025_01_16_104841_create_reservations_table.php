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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('correlative')->nullable();
            $table->string('name')->nullable();
            $table->string('reservation_datetime')->nullable();
            
            $table->string('nro_people')->default("1")->nullable();
            $table->string('status')->default('Reservado')->nullable();
          
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            $table->foreignId('event_id')->nullable()->unsigned()->constrained('events');
            $table->foreignId('station_id')->nullable()->unsigned()->constrained('stations');
            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');

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
        Schema::dropIfExists('reservervations');
    }
};
