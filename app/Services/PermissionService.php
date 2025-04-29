<?php
namespace App\Services;

use App\Models\Permission;
use App\Models\Permission_rol;
use App\Models\Rol;

class PermissionService
{

    public function getPermissionById(int $id): ?Permission
    {
        return Permission::find($id);
    }


    public function createPermission(array $data): Permission
    {
        $data['status']='Activo';
        $Permission = Permission::create($data);

        return $Permission;
    }

    public function updatePermission($Permission, array $data)
    {
        $filteredData = array_intersect_key($data, $Permission->getAttributes());
        $Permission->update($filteredData);
        return $Permission;
    }

    public function updatePermissionstatus($Permission, string $status)
    {
        $Permission->update(["status" => $status]);
        return $Permission;
    }

    public function destroyById($id)
    {
        $Permission = Permission::find($id);

        if (! $Permission) {
            return false;
        }
        return $Permission->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
