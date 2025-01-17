<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('people')->insert([
            'id' => 1,
            'type_document' => "DNI",
            'type_person' => "NATURAL",
            'number_document' => "00000000",
            'names' => "ADMINISTRADOR",
            'father_surname' => "",
            'mother_surname' => "",
            'business_name' => "",

            'address' => "",
            'phone' => "999999999",
            'email' => "",

            'ocupation' => "Administrador",
            'status' => "Activo",
      

        ]);

        DB::table('users')->insert([
            'id' => 1, // El ID del usuario
            'name' => 'ADMINISTRADOR', // Nombre del usuario
            'status' => "Activo",
            'username' => 'admin', // Correo electrónico
            'password' => Hash::make('admingia'), // Contraseña hasheada usando Hash::make
            'rol_id' => 1,
            'person_id' => 1, // Relacionar con el ID de la persona
        ]);

    }
}
