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
        Schema::create('people', function (Blueprint $table) {
            $table->id(); // Genera una columna 'id' autoincremental
            $table->string('type_document')->nullable(); // Tipo de documento, puede ser nulo
            $table->string('type_person')->nullable(); // Tipo de Persona
            $table->string('number_document')->nullable(); // Número de documento, debe ser único
            $table->string('names')->nullable(); // Nombres
            $table->string('father_surname')->nullable(); // Apellido del padre
            $table->string('mother_surname')->nullable(); // Apellido de la madre
            $table->string('business_name')->nullable(); // Razón social o nombre del negocio, puede ser nulo
           
            $table->string('address')->nullable(); // Dirección, puede ser nula
            $table->string('phone')->nullable(); // Teléfono, puede ser nulo
            $table->string('email')->nullable(); // Correo electrónico, debe ser único
         
            $table->string('ocupation')->nullable(); // Ocupación, puede ser nulo
            $table->string('status')->default('Activo')->nullable();
            $table->string('server_id')->nullable(); // id del servidor
            $table->timestamps(); // 'created_at' y 'updated_at'
            $table->softDeletes(); // Agrega el campo 'deleted_at' para el soft delete
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
};
