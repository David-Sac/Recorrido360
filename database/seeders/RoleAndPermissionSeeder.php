<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'componentes.view',
            'componentes.manage',
            'elementos.manage',
            'panoramas.view',
            'panoramas.manage',
            'hotspots.manage',
            'recorridos.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'componentes.view',
            'componentes.manage',
            'elementos.manage',
            'panoramas.view',
            'panoramas.manage',
            'hotspots.manage',
            'recorridos.manage',
        ]);

        $assistant = Role::firstOrCreate(['name' => 'Asistente']);
        $assistant->syncPermissions([
            'panoramas.view',
            'panoramas.manage',
            'hotspots.manage',
        ]);

        $visitor = Role::firstOrCreate(['name' => 'Visitante']);
        $visitor->syncPermissions([
            'componentes.view',
            'panoramas.view',
        ]);
    }
}
