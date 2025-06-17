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
        Schema::create('code_assets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();          // Código original visible
            $table->text('encrypted')->nullable();                 // Valor cifrado
            $table->string('barcode_path')->nullable();
            $table->string('qrcode_path')->nullable();

            $table->string('description')->nullable();
            $table->foreignId('lottery_ticket_id')->nullable()->unsigned()->constrained('lottery_tickets');
            $table->foreignId('reservation_id')->nullable()->unsigned()->constrained('reservations');
            $table->foreignId('entry_id')->nullable()->unsigned()->constrained('entries');

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
        Schema::dropIfExists('code_assets');
    }
};
