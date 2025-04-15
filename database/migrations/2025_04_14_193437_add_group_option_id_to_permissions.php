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
        Schema::table('permissions', function (Blueprint $table) {
            $table->foreignId('group_option_id')->nullable()->unsigned()->constrained('group_options');
            $table->string('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            
            if (Schema::hasColumn('permissions', 'group_option_id')) {
                $table->dropForeign(['group_option_id']);
                $table->dropColumn('group_option_id');
            }
            $table->dropColumn('link');
        });
    }
};
