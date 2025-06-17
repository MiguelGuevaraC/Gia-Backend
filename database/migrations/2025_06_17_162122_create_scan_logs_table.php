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
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('status')->nullable(); // 'ok', 'denied', etc.
            $table->text('description')->nullable();
            $table->foreignId('code_asset_id')->nullable()->unsigned()->constrained('code_assets');
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
        Schema::dropIfExists('scan_logs');
    }
};
