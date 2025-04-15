<?php
namespace Database\Seeders;

use App\Models\GroupOption;
use App\Models\Permission;
use App\Models\Permission_rol;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class AsignarRolesConOpciones extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_grupos = [
            ['id' => 1, 'name' => 'Regresar', 'icon' => 'ChevronLeft', 'link' => '/'],
            ['id' => 2, 'name' => 'Inicio', 'icon' => 'Home', 'link' => '/'],
            ['id' => 3, 'name' => 'Usuarios', 'icon' => 'User', 'link' => '/usuarios'],
            ['id' => 4, 'name' => 'Ajustes', 'icon' => 'Settings', 'link' => '/empresas'],
            ['id' => 5, 'name' => 'Eventos', 'icon' => 'Drama', 'link' => '/'],
            ['id' => 6, 'name' => 'Reservas', 'icon' => 'Calendar', 'link' => '/'],
            ['id' => 7, 'name' => 'Sorteos', 'icon' => 'Gift', 'link' => '/'],
        ];
    
        foreach ($data_grupos as $grupo) {
            GroupOption::updateOrCreate(
                ['id' => $grupo['id']],
                $grupo
            );
        }
    
        // Permisos para el grupo "Usuarios" (id 3)
        $data_opciones = [
            ['id' => 5, 'name' => 'Leer', 'link' => '/usuarios', 'group_option_id' => 3],
            ['id' => 6, 'name' => 'Leer Roles', 'link' => '/usuarios/roles', 'group_option_id' => 3],
        ];
    
        foreach ($data_opciones as $permiso) {
            Permission::updateOrCreate(
                ['id' => $permiso['id']],
                [
                    'name' => $permiso['name'],
                    'link' => $permiso['link'],
                    'group_option_id' => $permiso['group_option_id'],
                ]
            );
        }
    
        // Simulación: obtener rol administrador (ajústalo según tu app)
        $role = Rol::firstOrCreate(['name' => 'Admin']); // ajusta si es necesario
    
        // IDs de permisos válidos que deseas asignar al rol
        $validPermissions = [8, 9];
    
        foreach ($validPermissions as $permission_id) {
            $permission = Permission::find(1);
    
            if ($permission) {
                Permission_rol::create([
                    'name_permission' => $permission->name,
                    'name_rol'        => $role->name,
                    'rol_id'          => $role->id,
                    'permission_id'   => $permission->id,
                ]);
            }
        }
    }
    

}
