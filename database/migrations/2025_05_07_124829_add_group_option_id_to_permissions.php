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
        if (!Schema::hasColumn('permissions', 'group_option_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->foreignId('group_option_id')
                      ->nullable()
                      ->constrained('group_options')
                      ->after('id'); // Puedes ajustar la posición si lo necesitas
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('permissions', 'group_option_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropForeign(['group_option_id']);
                $table->dropColumn('group_option_id');
            });
        }
    }
};
