<?php

namespace App\Repositories\RolesPersmissions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class RolesPermissionsRepository implements RolesPersmissionsRepositoryInterface
{
    protected $rolesFilePath = 'roles-permissions/roles.json';
    protected $permissionsFilePath = 'roles-permissions/permissions.json';

    public function getAllRolesWithPermissions(): Collection
    {
        $dataRoles = $this->getAllRoles();
        if(count($dataRoles) > 0){
            $dataPermissions= $this->getPermissionsCollection();
            $dataRoles = $dataRoles->map(function ($role) use ($dataPermissions) {
                $new_permissions = [];
                $role_permissions = $role['permissions'] ?? [];
                foreach ($role_permissions as $permissionId) {
                    $permission = $dataPermissions->where('id', $permissionId)->first();
                    if($permission){
                        $new_permissions[] = $permission;
                    }
                }

                $role['permissions'] = $new_permissions;
                return $role;
            });

        }
        return $dataRoles;
    }

    public function getAllPermissions(): Collection
    {
        return $this->getPermissionsCollection();
    }

    /**
     * Renvoie le role avec ses permissions liées
     * @param $roleId
     * @return array|null
     */
    public function getRoleByIdWithPermissions($roleId): array|null
    {
        $role = $this->getAllRoles()->where('id', $roleId)->first();
        if($role){
            $dataPermissions= $this->getPermissionsCollection();
            $role_permissions = $role['permissions'] ?? [];
            $new_permissions = [];
            foreach ($role_permissions as $permissionId) {
                $permission = $dataPermissions->where('id', $permissionId)->first();
                if($permission){
                    $new_permissions[] = $permission;
                }
            }
            $role['permissions'] = array_column($new_permissions, 'internal_name');
        }
        return($role);
    }



    public function getAllRoles(): Collection
    {
        $jsonRoleContent = Storage::get($this->rolesFilePath);
        $dataRoles = json_decode($jsonRoleContent, true);
        $dataRoles = $dataRoles['roles'] ?? [];
        foreach ($dataRoles as &$r) {
            $r['name'] = __('bugtrackly.general.' . $r['internal_name'].'_label');
        }
        return collect($dataRoles);
    }

    private function getPermissionsCollection()
    {
        $jsonPermissionContent = Storage::get($this->permissionsFilePath);
        $dataPermissions = json_decode($jsonPermissionContent, true);
        return collect($dataPermissions['permissions'] ?? []);
    }
}
