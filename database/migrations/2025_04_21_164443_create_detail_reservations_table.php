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
        Schema::create('detail_reservations', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->integer('cant')->nullable();
            $table->string('type')->nullable();
            $table->decimal('precio')->nullable();
            //$table->decimal('precio_total')->nullable();

            $table->string('status')->default('Pendiente')->nullable();

            $table->foreignId('reservation_id')->nullable()->unsigned()
                ->constrained('reservations');

            $table->foreignId('promotion_id')->nullable()->unsigned()
                ->constrained('promotions');

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
        Schema::dropIfExists('detail_reservations');
    }
};
