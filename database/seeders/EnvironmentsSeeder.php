<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnvironmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('environments')->insert([
            'id'          => 1,
            'name'        => "Salon Recepción",
            'description' => "Salon Recepción",
            'route'       => "https://develop.garzasoft.com/Gia-Backend/public/storage/companies/2_20250116_171554.jpg",
            'status'      => 1,
            'company_id'  => 1,
        ]);
        DB::table('environments')->insert([
            'id'          => 2,
            'name'        => "Salon Principal",
            'description' => "Salon Principal",
            'route'       => "https://develop.garzasoft.com/Gia-Backend/public/storage/environments/9_20250117_031726.png",
            'status'      => 1,
            'company_id'  => 1,
        ]);
    }
}
