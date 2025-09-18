<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\AcademicYear;
use App\Models\MClass;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $siswaRole     = Role::where('name', 'Siswa')->first();
        $academicYear  = AcademicYear::orderBy('year', 'desc')->first();
        $classes       = MClass::all();

        if (!$siswaRole || !$academicYear || $classes->isEmpty()) {
            $this->command->warn('⚠️ Role "Siswa", AcademicYear, atau Class belum ada. Jalankan DatabaseSeeder dulu.');
            return;
        }

        $students = [
            ['first_name' => 'Budi',  'last_name' => 'Santoso', 'parent_status' => 'Lengkap'],
            ['first_name' => 'Ani',   'last_name' => 'Wijaya',  'parent_status' => 'Yatim'],
            ['first_name' => 'Dewi',  'last_name' => 'Lestari', 'parent_status' => 'Piatu'],
            ['first_name' => 'Andi',  'last_name' => 'Saputra', 'parent_status' => 'Lengkap'],
            ['first_name' => 'Siti',  'last_name' => 'Nurmala', 'parent_status' => 'Yatim Piatu'],
            ['first_name' => 'Rudi',  'last_name' => 'Hartono', 'parent_status' => 'Lengkap'],
            ['first_name' => 'Lina',  'last_name' => 'Kusuma',  'parent_status' => 'Lengkap'],
            ['first_name' => 'Agus',  'last_name' => 'Pratama', 'parent_status' => 'Yatim'],
            ['first_name' => 'Fitri', 'last_name' => 'Amalia',  'parent_status' => 'Lengkap'],
            ['first_name' => 'Joko',  'last_name' => 'Susilo',  'parent_status' => 'Piatu'],
        ];

        // List nama orang tua
        $parents = [
            'Slamet', 'Sri', 'Eko', 'Wati', 'Bambang',
            'Ratna', 'Darto', 'Sulastri', 'Gunawan', 'Nurhayati'
        ];

        foreach ($students as $index => $student) {
            $class = $classes->random(); // pilih kelas random
            $parentName = $parents[array_rand($parents)];

            // Generate NISN (tahun ajaran + 6 angka random)
            $nisn = preg_replace('/[^0-9]/', '', $academicYear->year) 
                . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            User::create([
                'id'               => Str::uuid(),
                'role_id'          => $siswaRole->id,
                'class_id'         => $class->id,
                'first_name'       => $student['first_name'],
                'last_name'        => $student['last_name'],
                'gender'           => $index % 2 === 0 ? 'Pria' : 'Wanita',
                'phone'            => '0812' . rand(10000000, 99999999),
                'address'          => 'Alamat ' . $student['first_name'],
                'email'            => strtolower($student['first_name']) . '@gmail.com',
                'password'         => Hash::make('password'),
                'academic_year_id' => $academicYear->id,
                'nisn'             => $nisn, // ✅ NISN otomatis
                'parent_status'    => $student['parent_status'],
                'parent_name'      => $parentName,
            ]);
            // ✅ Observer akan buat 12 transaksi otomatis
        }
    }
}