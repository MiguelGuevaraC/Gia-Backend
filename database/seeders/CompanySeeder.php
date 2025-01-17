<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('companies')->insert([
            'id' => 1,
            'ruc'=> '11111111111',
            'business_name'=> 'Gia  Lounge',
            'address'=> 'Av. Chinchaysuyo 1217, La Victoria 14008',
            'phone'=> '997640789',
            'email'=> 'gialounge@test.com',
            'route'=> 'https://develop.garzasoft.com/Gia-Backend/public/storage/companies/4_20250117_085741.jpg',
            'status'=> 'Activo',
        ]);


    }
}
