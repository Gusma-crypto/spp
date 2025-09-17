<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\MClass;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        $superAdmin    = Role::create(['id' => Str::uuid(), 'name' => 'Super Admin']);
        $bendahara     = Role::create(['id' => Str::uuid(), 'name' => 'Bendahara']);
        $kepalaSekolah = Role::create(['id' => Str::uuid(), 'name' => 'Kepala Sekolah']);
        $siswaRole     = Role::create(['id' => Str::uuid(), 'name' => 'Siswa']);

        // Users admin
        User::create([
            'id'         => Str::uuid(),
            'role_id'    => $superAdmin->id,
            'first_name' => 'Uni',
            'last_name'  => 'Andriani Sitohang',
            'gender'     => 'Wanita',
            'phone'      => '082251464211',
            'address'    => '-',
            'email'      => 'super.admin@gmail.com',
            'password'   => Hash::make('password'),
        ]);

        User::create([
            'id'         => Str::uuid(),
            'role_id'    => $bendahara->id,
            'first_name' => 'Bendahara',
            'last_name'  => 'Sekolah',
            'gender'     => 'Tidak Diketahui',
            'phone'      => '-',
            'address'    => '-',
            'email'      => 'bendahara@gmail.com',
            'password'   => Hash::make('password'),
        ]);

        User::create([
            'id'         => Str::uuid(),
            'role_id'    => $kepalaSekolah->id,
            'first_name' => 'Kepala',
            'last_name'  => 'Sekolah',
            'gender'     => 'Tidak Diketahui',
            'phone'      => '-',
            'address'    => '-',
            'email'      => 'kepala.sekolah@gmail.com',
            'password'   => Hash::make('password'),
        ]);

        // Classes
        $classes = [
            'X IPA 1', 'X IPA 2',
            'XI IPA 1', 'XI IPA 2',
            'XII IPA 1', 'XII IPA 2',
            'X IPS 1', 'XI IPS 1', 'XII IPS 1',
        ];

        foreach ($classes as $class) {
            MClass::create([
                'id'   => Str::uuid(),
                'name' => $class,
            ]);
        }

        // Academic Years
        $academicYears = ['2025/2026'];

        foreach ($academicYears as $year) {
            AcademicYear::create([
                'id'   => Str::uuid(),
                'year' => $year,
            ]);
        }

        // ✅ Panggil Seeder siswa
        $this->call([
            SiswaSeeder::class,
        ]);
    }
}