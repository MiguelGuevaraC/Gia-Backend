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
        Schema::create('lottery_by_events', function (Blueprint $table) {
            $table->id();
            $table->decimal('price_factor_consumo')->nullable();
            $table->foreignId('lottery_id')->nullable()->unsigned()->constrained('lotteries');
            $table->foreignId('event_id')->nullable()->unsigned()->constrained('events');
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
        Schema::dropIfExists('lottery_by_events');
    }
};
