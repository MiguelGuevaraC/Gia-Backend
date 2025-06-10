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
        Schema::create('lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code_correlative')->nullable();
            $table->string('reason')->nullable();
            $table->string('status')->default('Pendiente');
            $table->foreignId('user_owner_id')->nullable()->unsigned()->constrained('users');
            $table->foreignId('lottery_id')->nullable()->unsigned()->constrained('lotteries');
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
        Schema::dropIfExists('lottery_tickets');
    }
};
