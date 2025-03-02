<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       
        $this->call(PermissionSeeder::class);
        $this->call(TypeUserAccessSeeder::class);
        $this->call(UsersSeeders::class);
        $this->call(CompanySeeder::class);
        $this->call(EnvironmentsSeeder::class);
        $this->call(StationSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(ReservaSeeder::class);
        $this->call(EntrySeeder::class);
    }
}
