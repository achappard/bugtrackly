<?php

namespace App\Trait\User;

use App\Models\Permission;
use App\Repositories\RolePersmission\RolePersmissionRepositoryInterface;
use App\Repositories\RolesPersmissions\RolesPersmissionsRepositoryInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;


trait HasRoleAndPersmissionTrait
{
    /**
     * Retourne le role de l'utilisateur
     * @return Attribute
     */
    protected function role(): Attribute{
        $roleRepository = app(RolesPersmissionsRepositoryInterface::class);

        return new Attribute(
            get: fn () => $roleRepository->getRoleByIdWithPermissions($this->role_id),
        );
    }

    /**
     * Retourne le role de l'utilisateur
     * @return Attribute
     */
    protected function roleName(): Attribute{
        $roleRepository = app(RolesPersmissionsRepositoryInterface::class);

        return new Attribute(
            get: fn () => $this->role['name'],
        );
    }

    /**
     * Check role
     * @param $roleSlug
     * @return bool
     */
    public function hasRole($roleSlug): bool
    {
        $role = $this->getRoleAttribute();
        return $role['internal_name'] === $roleSlug;
    }

    /**
     * Check permission
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission): bool
    {
        return in_array($permission, $this->role['permissions']);
    }
}
