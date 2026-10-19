<?php

namespace Database\Seeders;

use App\Domain\Identity\Enums\Role as RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /** Permission => rôles qui la reçoivent (l'admin a tout via Gate::before). */
    public const MATRIX = [
        'backoffice.access' => [RoleName::Listener, RoleName::Supervisor, RoleName::Professional],
        'conversations.listen' => [RoleName::Listener, RoleName::Supervisor],
        'conversations.supervise' => [RoleName::Supervisor],
        'alerts.manage' => [RoleName::Supervisor],
        'reports.moderate' => [RoleName::Supervisor],
        'users.suspend' => [RoleName::Supervisor],
        'users.manage' => [],
        'professionals.manage' => [],
        'bookings.manage' => [],
        'payments.view' => [],
        'courses.manage' => [],
        'plans.manage' => [],
        'emergency-contacts.manage' => [],
        'activity.view' => [],
        'content.manage' => [RoleName::Supervisor],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RoleName::cases() as $role) {
            Role::findOrCreate($role->value, 'web');
        }

        foreach (self::MATRIX as $permission => $roles) {
            Permission::findOrCreate($permission, 'web');

            foreach ($roles as $role) {
                Role::findByName($role->value, 'web')->givePermissionTo($permission);
            }
        }
    }
}
