<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdmin = Role::create([
            'name'      => 'Super Admin'
        ]);

        $bendahara = Role::create([
            'name'      => 'Bendahara'
        ]);

        $kepalaSekolah = Role::create([
            'name'      => 'Kepala Sekolah'
        ]);

        $siswa = Role::create([
            'name'      => 'Siswa'
        ]);

        User::create([
            'role_id'       => $superAdmin->id,
            'first_name'    => 'Uni',
            'last_name'     => 'Andriani Sitohang',
            'gender'        => 'Wanita',
            'phone'         => '082251464211',
            'address'       => '-',
            'email'         => 'super.admin@gmail.com',
            'password'      => Hash::make('password')
        ]);

        User::create([
            'role_id'       => $bendahara->id,
            'first_name'    => 'Bendahara',
            'last_name'     => 'Sekolah',
            'gender'        => 'Tidak Diketahui',
            'phone'         => '-',
            'address'       => '-',
            'email'         => 'bendahara@gmail.com',
            'password'      => Hash::make('password')
        ]);

        User::create([
            'role_id'       => $kepalaSekolah->id,
            'first_name'    => 'Kepala',
            'last_name'     => 'Sekolah',
            'gender'        => 'Tidak Diketahui',
            'phone'         => '-',
            'address'       => '-',
            'email'         => 'kepala.sekolah@gmail.com',
            'password'      => Hash::make('password')
        ]);
    }
}
