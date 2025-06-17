<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Crear tabla de premios
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
             $table->text('description')->nullable();
            $table->text('route')->nullable();
            $table->foreignId('lottery_id')->nullable()->constrained('lotteries')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Añadir columna "route" a la tabla "lotteries" si no existe
        if (!Schema::hasColumn('lotteries', 'route')) {
            Schema::table('lotteries', function (Blueprint $table) {
                $table->text('route')->nullable()->after('status'); // ajusta la posición si es necesario
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Eliminar tabla de premios
        Schema::dropIfExists('prizes');

        // Eliminar columna "route" de la tabla "lotteries"
        if (Schema::hasColumn('lotteries', 'route')) {
            Schema::table('lotteries', function (Blueprint $table) {
                $table->dropColumn('route');
            });
        }
    }
};
