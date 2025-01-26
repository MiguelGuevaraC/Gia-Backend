<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('stations')->insert([
            'id'             => 1,
            'name'           => "MESA 01",
            'description'    => "Mesa para 6 personas",
            'type'           => "MESA",
            'status'         => "Disponible",
            'route'          => "https://develop.garzasoft.com/Gia-Backend/public/storage/environments/1_20250112_015731.jpg",
            'environment_id' => 1,
        ]);

        DB::table('stations')->insert([
            'id'             => 2,
            'name'           => "MESA 02",
            'description'    => "Mesa para 8 personas",
            'type'           => "MESA",
            'status'         => "Disponible",
            'route'          => "https://develop.garzasoft.com/Gia-Backend/public/storage/environments/1_20250112_015731.jpg",
            'environment_id' => 2,
        ]);
        DB::table('stations')->insert([
            'id'             => 3,
            'name'           => "MESA 03",
            'description'    => "Mesa para 5 personas",
            'type'           => "MESA",
            'status'         => "Disponible",
            'route'          => "https://develop.garzasoft.com/Gia-Backend/public/storage/environments/1_20250112_015731.jpg",
            'environment_id' => 2,
        ]);

        DB::table('stations')->insert([
            'id'             => 4,
            'name'           => "MESA 04",
            'description'    => "Mesa para 4 personas",
            'type'           => "MESA",
            'status'         => "Disponible",
            'route'          => "https://develop.garzasoft.com/Gia-Backend/public/storage/environments/1_20250112_015731.jpg",
            'environment_id' => 2,
        ]);
    }
}
