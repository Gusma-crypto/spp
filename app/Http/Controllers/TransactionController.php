<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\MClass;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $class = urldecode($request->query("class"));

            $user = User::selectRaw('users.*, roles.name AS role_name')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.id', Auth::user()->id)
                ->first();

            if ($user->role_name === "Siswa") {
                return redirect()->route('spp.transaction.show', ['id' => $user->id]);
            }

            $data = User::selectRaw("users.*, academic_years.year, classes.name AS class_name")
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->leftJoin('academic_years', 'users.academic_year_id', '=', 'academic_years.id')
                ->leftJoin('classes', 'users.class_id', '=', 'classes.id')
                ->where('roles.name', '=', 'Siswa')
                ->when($class, function ($query, $class) {
                    $query->where('classes.name', $class);
                })
                ->get();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            $classes = MClass::orderBy('name', 'asc')->get();

            return view("pages.transaction.index", compact('data', 'classes', 'class'));
        } catch (\Throwable $th) {
            return redirect()->route('spp.transaction.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $transaction = Transaction::find($data['transaction_id']);

            $transaction->update([
                'type'          => "Payment Gateway",
                'status'        => "Pending"
            ]);

            $snapParams = array(
                'transaction_details' => array(
                    'order_id'      => rand(),
                    'gross_amount'  => $transaction->price,
                )
            );

            $snapToken = \Midtrans\Snap::getSnapToken($snapParams);

            $transaction->snap_token = $snapToken;
            $transaction->save();

            return response()->json($transaction);
        }  catch (ValidationException $e) {
            return response()->json($e->errors(), 500);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function manualStore(Request $request)
    {
        try {
            $data = $request->all();

            $transaction = Transaction::find($data['transaction_id']);
            //update status to OK and type to manual
            $transaction->update([
                'type'          => "Manual",
                'status'        => "OK"
            ]);

            // ambil nama siswa
            $student = User::find($transaction->student_id);

            // ambil bulan dari kolom date
            $monthName = $transaction->date
                ? Carbon::parse($transaction->date)->translatedFormat('F')
                : Carbon::now()->translatedFormat('F');

            // catat ke income
            Income::create([
                'date'         => now(),
                'total_income' => $data['purchase'],
                'note'         => "Pembayaran SPP - {$student->first_name} {$student->last_name} bulan {$monthName}  - via Manual"
            ]);

            return response()->json([
                'status'  => "OK",
                'message' => "Pembayaran manual berhasil dicatat",
                'data'    => $transaction
            ]);

            return response()->json($transaction);
        }  catch (ValidationException $e) {
            return response()->json($e->errors(), 500);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /** 
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        try {
            $year = urldecode($request->query("year"));

            $student = User::selectRaw("users.*, classes.name AS class_name, academic_years.year")
                ->join('classes', 'classes.id', '=', 'users.class_id')
                ->join('academic_years', 'academic_years.id', '=', 'users.academic_year_id')
                ->where('users.id', $id)
                ->first();

            $data = Transaction::select("*")
                ->where('student_id', '=', $id)
                ->where('year', !$year ? $student->year : $year)
                ->get();

            $academicYears = AcademicYear::all();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            return view("pages.transaction.detail", compact('data', 'student', 'academicYears', 'year'));
        } catch (\Throwable $th) {
            return redirect()->route('spp.transaction.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       try {
            $transaction = Transaction::findOrFail($id);

            // update status transaksi
            $transaction->update([
                'status' => "OK"
            ]);
            
            // ambil nama siswa
            $student = User::find($transaction->student_id);

            // ambil bulan dari kolom date  
            $monthName = $transaction->date
                ? Carbon::parse($transaction->date)->translatedFormat('F')
                : Carbon::now()->translatedFormat('F');
            // catat ke tabel incomes
            Income::create([
                'date'         => now(),
                'total_income' => $transaction->price,
                'note'         => 'Pembayaran SPP - '.$student->first_name.' '.$student->last_name.' bulan '.$monthName. ' - via ' . $transaction->type,
            ]);

            Log::info("Transaksi berhasil diupdate & masuk income", [
                'transaction_id' => $transaction->id,
                'student_id'     => $transaction->student_id,
                'amount'         => $transaction->price,
                'type'           => $transaction->type
            ]);
            
            return response()->json([
                'status'  => "OK",
                'message' => "Transaksi berhasil diupdate dan dicatat ke income",
                'data'    => $transaction
            ]);
        } catch (ValidationException $e) {
            Log::error("Validasi gagal saat update transaksi", [
                'errors'  => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json($e->errors(), 500);
        } catch (\Throwable $th) {
            Log::error("Error saat update transaksi", [
                'transaction_id' => $id,
                'error'          => $th->getMessage(),
                'trace'          => $th->getTraceAsString(),
            ]);
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
