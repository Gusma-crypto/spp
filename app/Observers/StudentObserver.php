<?php

namespace App\Observers;

use App\Models\AcademicYear;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class StudentObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            $newPrice = 50000; // Default value harga spp adalah Rp 50.0000
            $discountValue = 0;

            $siswaRole = Role::where('name', 'Siswa')->first();

            if ($user->role_id !== $siswaRole->id) return;

            $academicYear = AcademicYear::find($user->academic_year_id);
            $tahunAjaran = $academicYear->year;
            [$startYear, $endYear] = explode('/', $tahunAjaran);

            if ($user->parent_status === "Lengkap") {
                $newPrice = 50000;
            } else if ($user->parent_status === "Yatim" || $user->parent_status === "Piatu") {
                $discountValue = (75 / 100) * 50000; // Nilai Diskon
                $newPrice = 50000 - $discountValue;
            } else {
                $newPrice = 0;
            }

            for ($i = 0; $i < 12; $i++) {
                $bulan = ($i % 12) + 7;
                $tahun = $startYear;

                if ($bulan > 12) {
                    $bulan -= 12;
                    $tahun = $endYear;
                }

                Transaction::create([
                    'student_id'    => $user->id,
                    'year'          => $academicYear->year,
                    'date'          => Carbon::createFromDate($tahun, $bulan, 10),
                    'price'         => $newPrice,
                    'type'          => $user->parent_status === "Yatim Piatu" ? 'Otomatis Dari Sistem' : '-',
                    'status'        => $user->parent_status === "Yatim Piatu" ? 'Lunas' : 'Belum Lunas',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /** 
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
