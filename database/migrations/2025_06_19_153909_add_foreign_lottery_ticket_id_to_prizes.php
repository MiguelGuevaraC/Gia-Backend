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
        Schema::table('prizes', function (Blueprint $table) {
            $table->foreignId('lottery_ticket_id')
                ->nullable()->unsigned()->constrained('lottery_tickets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prizes', function (Blueprint $table) {
            Schema::table('prizes', function (Blueprint $table) {
                $table->dropForeign(['lottery_ticket_id']);
                $table->dropColumn('lottery_ticket_id');
            });
        });
    }
};
