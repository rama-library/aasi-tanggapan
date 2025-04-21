<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role sudah ada
        $roles = ['Main Admin', 'Reviewer', 'PIC'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Rama Perdana - Main Admin
        $rama = User::firstOrCreate(
            ['email' => 'rama.perdana@aasi.or.id'],
            [
                'name' => 'Rama Perdana',
                'company_name' => 'AASI',
                'department' => 'IT',
                'password' => Hash::make('password'), // Ganti di production
            ]
        );
        $rama->assignRole('Main Admin');

        // Gita Pratiwi - Reviewer
        $gita = User::firstOrCreate(
            ['email' => 'gita.pratiwi@aasi.or.id'],
            [
                'name' => 'Gita Pratiwi',
                'company_name' => 'AASI',
                'department' => 'LEGAL',
                'password' => Hash::make('password'),
            ]
        );
        $gita->assignRole('Reviewer');

        // Rian Andreas Abidin - PIC
        $rian = User::firstOrCreate(
            ['email' => 'rian@aasi.or.id'],
            [
                'name' => 'Rian Andreas Abidin',
                'company_name' => 'AASI',
                'department' => 'IT',
                'password' => Hash::make('password'),
            ]
        );
        $rian->assignRole('PIC');

        // Rian Andreas Abidin - PIC
        $dummy = User::firstOrCreate(
            ['email' => 'dummy@aasi.or.id'],
            [
                'name' => 'Dummy',
                'company_name' => 'AASI',
                'department' => 'IT',
                'password' => Hash::make('password'),
            ]
        );
        $dummy->assignRole('Main Admin');
    }
}
