<?php

use App\Exports\SppPerSiswaExport;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ReportController; 
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        // Route::get('/', [HomeController::class, 'index'])->name('dashboard');
        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard');
            Route::get('/export/unpaid', 'unpaidExport')->name('export.unpaid');
        });
        
        Route::prefix('data-pemasukan')->group(function () {
            Route::controller(IncomeController::class)->group(function () {
                Route::get('/', 'index')->name('master.income.index');
                Route::get('/create', 'create')->name('master.income.create');
                // Route::get('/groupByDate', 'groupByDate')->name('master.income.groupByDate');
                Route::post('/store', 'store')->name('master.income.store');
                Route::get('/edit/{id}', 'edit')->name('master.income.edit');
                Route::patch('/update/{id}', 'update')->name('master.income.update');
                Route::delete('/destroy/${id}', 'destroy')->name('master.income.destroy');
            });
        });

        Route::prefix('data-pengeluaran')->group(function () {
            Route::controller(ExpenseController::class)->group(function () {
                Route::get('/', 'index')->name('master.expense.index');
                Route::get('/create', 'create')->name('master.expense.create');
                Route::post('/expense/{id}/approve', 'approve')->name('master.expense.approve');
                Route::post('/expense/{id}/reject', 'reject')->name('master.expense.reject');
                Route::post('/store', 'store')->name('master.expense.store');
                Route::get('/edit/{id}', 'edit')->name('master.expense.edit');
                Route::patch('/update/{id}', 'update')->name('master.expense.update');
                Route::delete('/destroy/${id}', 'destroy')->name('master.expense.destroy');
            });
        });

        Route::prefix('data-kelas')->group(function () {
            Route::controller(ClassController::class)->group(function () {
                Route::get('/', 'index')->name('master.class.index');
                Route::get('/create', 'create')->name('master.class.create');
                Route::post('/store', 'store')->name('master.class.store');
                Route::get('/edit/{id}', 'edit')->name('master.class.edit');
                Route::patch('/update/{id}', 'update')->name('master.class.update');
                Route::delete('/destroy/${id}', 'destroy')->name('master.class.destroy');
            });
        });

        Route::prefix('data-tahun-ajaran')->group(function () {
            Route::controller(AcademicYearController::class)->group(function () {
                Route::get('/', 'index')->name('master.year.index');
                Route::get('/create', 'create')->name('master.year.create');
                Route::post('/store', 'store')->name('master.year.store');
                Route::get('/edit/{id}', 'edit')->name('master.year.edit');
                Route::patch('/update/{id}', 'update')->name('master.year.update');
                Route::delete('/destroy/${id}', 'destroy')->name('master.year.destroy');
            });
        });

        Route::prefix('data-pengguna-aplikasi')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/', 'index')->name('master.user.index');
                Route::get('/create', 'create')->name('master.user.create');
                Route::post('/store', 'store')->name('master.user.store');
                Route::get('/edit/{id}', 'edit')->name('master.user.edit');
                Route::patch('/update/{id}', 'update')->name('master.user.update');
                Route::delete('/destroy/${id}', 'destroy')->name('master.user.destroy');
            });
        });
 
        Route::prefix('data-siswa')->group(function () {
            Route::controller(StudentController::class)->group(function () {
                Route::get('/', 'index')->name('master.student.index');
                Route::get('/create', 'create')->name('master.student.create');
                Route::post('/store', 'store')->name('master.student.store');
                Route::get('/edit/{id}', 'edit')->name('master.student.edit');
                Route::patch('/update/{id}', 'update')->name('master.student.update');
                Route::delete('/destroy/${id}', 'destroy')->name('master.student.destroy');
            });
        });

        Route::prefix('transaksi-spp')->group(function () {
            Route::controller(TransactionController::class)->group(function () {
                Route::get('/', 'index')->name('spp.transaction.index');
                Route::get('/create', 'create')->name('spp.transaction.create');
                Route::post('/store', 'store')->name('spp.transaction.store');
                Route::post('/notification', 'notificationHandler')->name('spp.transaction.midtransNotification');
                Route::post('/manual-store', 'manualStore')->name('spp.transaction.manualStore');
                Route::get('/show/{id}', 'show')->name('spp.transaction.show');
                Route::get('/edit/{id}', 'edit')->name('spp.transaction.edit');
                Route::patch('/update/{id}', 'update')->name('spp.transaction.update');
                Route::delete('/destroy/${id}', 'destroy')->name('spp.transaction.destroy');
            });
        });  

        Route::prefix('laporan')->group(function () {
            Route::controller(ReportController::class)->group(function () {
                Route::get('/spp', 'index')->name('report.spp.index');
                Route::get('/spp/detail/{id}', 'laporanSPPDetail')->name('report.spp.detail');
                Route::get('/pemasukan', 'indexIncome')->name('report.income.index');
                Route::get('/export/income', 'exportIncomePdf')->name('report.export.incomepdf');
                Route::post('/export/income-rkp-excel', 'exportRkpExcel')->name('report.export.incomeexcel');
                Route::get('/grafik/income')->name('report.grafik.index');
                Route::get('/pemasukan/detail/{date}', 'detailIncome')->name('report.income.detail');
                Route::get('/pengeluaran', 'IndexExpense')->name('report.expense.index');
                Route::get('/export/student/{student}', 'exportStudent')->name('report.export.student');
                Route::get('/export/class', 'exportClass')->name('report.export.class');
                Route::get('/export/year/{year}', 'exportYear')->name('report.export.year');
                Route::post('/export/income', 'exportIncome')->name('report.export.income');
                Route::post('/export/incomePdf', 'exportIncomePdf')->name('report.export.incomePdf');
                Route::post('/export/expense', 'exportExpense')->name('report.export.expense');
                Route::get('/unpaid', 'unpaidReport')->name('report.unpaid.index');
                Route::post('/unpaid', 'unpaidReport')->name('report.unpaid.index');
                Route::get('/unpaid/details', 'unpaidDetails')->name('report.unpaid.details');
            }); 
        });
    });
});
