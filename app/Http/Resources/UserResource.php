<?php
namespace App\Http\Resources;

use App\Models\GroupOption;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
{
    /**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="email", type="string", example="miguel@gmail.com"),
 *     @OA\Property(property="person_id", type="integer", description="Person Id", example="1"),
 *     @OA\Property(property="person", ref="#/components/schemas/Person"),
 *     @OA\Property(property="rol_id", type="integer", description="Role Id", example="1"),
 *     @OA\Property(property="rol", ref="#/components/schemas/Rol"),
 *     @OA\Property(property="menu", type="array", items=@OA\Items(ref="#/components/schemas/GroupOption"))
 * )
 */
    public function toArray($request)
    {
        $menu = $this->menu($this->rol->permissions()->pluck('permission_id'));
        return [
            'id'        => $this->id,
            'name'      => $this->name ?? null,
            'username'  => $this->username ?? null,
            'person_id' => $this->person_id ?? null,
            'rol_id'    => $this->rol_id ?? null,
            'person'    => $this->person ? new PersonResource($this->person) : null,
            'rol'       => $this->rol ? new RolResource($this->rol) : null,
            'menu'      => $menu,
        ];

    }

    public function menu($data)
    {
        $rolePermissionIds = $data;
 
        $groupOptions = GroupOption::with(['permissions' => function ($query) use ($rolePermissionIds) {
            $query->whereIn('permissions.id', $rolePermissionIds);
        }])->get();
    
        $result = $groupOptions->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name, // si tiene un campo nombre
                'icon' => $group->icon, 
                'link' => $group->link, 
                'permissions' => $group->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'type' => $permission->type,
                        'link' => $permission->link,
                    ];
                }),
            ];
        });
    
        return $result;
    }
    
    
    
    

}
