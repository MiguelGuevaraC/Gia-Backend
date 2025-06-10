<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lotteries', function (Blueprint $table) {
            $table->id();
            $table->string('code_serie')->nullable();        // code_serie
            $table->string('lottery_name')->nullable();      // lottery_name
            $table->text('lottery_description')->nullable(); // lottery_description
            $table->dateTimeTz('lottery_date')->nullable();  // lottery_date
            $table->string('status')->nullable()->default('Pendiente');  // status
            $table->decimal('lottery_price')->nullable(); // lottery_price
            $table->foreignId('winner_id')->nullable()->unsigned()->constrained('users'); // winner_id
            $table->foreignId('user_created_id')->nullable()->unsigned()->constrained('users');       // user_created_id
            $table->foreignId('event_id')->nullable()->unsigned()->constrained('events'); // event_id

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
        Schema::dropIfExists('lotteries');
    }
};
