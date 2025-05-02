<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'id'        => '1',
                'name'        => 'Tiempo de Reserva',
                'description' => 'Duración en minutos que se mantiene una reserva antes de caducar.',
                'amount'      => 10, // Por ejemplo, 30 minutos
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'id'        => '2',
                'name'        => 'Porcentaje Descuento Producto',
                'description' => 'Descuento aplicado a productos en promoción.',
                'amount'      => 10, // Por ejemplo, 10%
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ]);
    }
}
