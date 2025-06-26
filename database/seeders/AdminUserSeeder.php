<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Recuperamos los roles ya creados
        $superRole = Role::where('name','Super Admin')->first();
        $adminRole = Role::where('name','Admin')->first();

        // Creamos (o recuperamos) el Super Admin
        $super = User::firstOrCreate(
            ['email' => 'superadmin@ecomuseo.test'],
            [
                'name'      => 'Super Administrador',
                'dni'       => '12345678',
                'phone'     => '999888777',
                'birthdate' => '1980-01-01',
                'password'  => bcrypt('super123'),
            ]
        );
        $super->assignRole($superRole);

        // Creamos (o recuperamos) el Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@ecomuseo.test'],
            [
                'name'      => 'Administrador',
                'dni'       => '87654321',
                'phone'     => '888777666',
                'birthdate' => '1990-05-15',
                'password'  => bcrypt('admin123'),
            ]
        );
        $admin->assignRole($adminRole);
    }
}
