<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateReservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear el evento para actualizar el estado de las reservas automáticamente
        DB::unprepared('
            CREATE DEFINER=`root`@`localhost` EVENT `auto_expire_reservations_caducate`
            ON SCHEDULE EVERY 1 SECOND
            STARTS "2025-04-23 20:58:16"
            ON COMPLETION NOT PRESERVE
            ENABLE
            DO
            UPDATE reservations
            SET status = "Caducado"
            WHERE status = "Pendiente Pago"
            AND expires_at <= NOW();
        ');

        // Asegúrate de que el evento se haya creado correctamente
        $this->command->info('El evento auto_expire_reservations ha sido creado correctamente.');
    }
}
