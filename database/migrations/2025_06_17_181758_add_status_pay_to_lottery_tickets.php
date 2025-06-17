<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->string('status_pay')->nullable()->default('Pendiente');
              $table->string('status_scan')->nullable()->default('Pendiente');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('status_pay')->nullable()->default('Pendiente');
              $table->string('status_scan')->nullable()->default('Pendiente');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->dropColumn('status_pay');
             $table->dropColumn('status_scan');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('status_pay');
             $table->dropColumn('status_scan');
        });
    }
};
