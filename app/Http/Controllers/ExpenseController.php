<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

use function PHPUnit\Framework\fileExists;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Ambil data descending berdasarkan tanggal (atau created_at)
            $data = Expense::orderBy('date', 'desc')->get();

            // Tambahkan nomor urut untuk tampil di blade
            $data->transform(function($item, $key) {
                $item->no = $key + 1;
                return $item;
            });
            return view("pages.expense.index", compact('data'));
        } catch (\Throwable $th) {
            return redirect()->route('master.expense.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.expense.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $attr = $request->validate([
                'date'          => 'required|date',
                'total_expense' => 'required|string',
                'note'          => 'required|string',
                'file'          => 'required|mimes:pdf,xlsx,xls,csv,doc,docx,png,jpeg,jpg,gif|max:10000'
            ]);

            $uuid = Uuid::uuid4();
            $fileName = Carbon::now()->format('Y-m-d')."_{$uuid}".'.'.$request->file->extension();
            $request->file->move(public_path('storage/expense_uploads'), $fileName);
            $relativePath = 'storage/expense_uploads/' . $fileName;

            Expense::create([
                'date'          => $attr['date'],
                'total_expense' => $attr['total_expense'],
                'note'          => $attr['note'],
                'file_path'     => $relativePath,
                'status'        => 'pending' // Set status awal sebagai 'pending'
            ]);

            return redirect()->route('master.expense.index')->with('success', 'Data pengeluaran berhasil disimpan.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.expense.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.expense.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show()
    // {
    //     try {
    //         $data = Expense::all();

    //         foreach ($data as $key => $x) {
    //             $x['no'] = $key + 1;
    //         }

    //         return view("pages.expense.index", compact('data'));
    //     } catch (\Throwable $th) {
    //         return redirect()->route('master.expense.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
    //     }
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = Expense::find($id);

            if ($data) {
                return view("pages.expense.edit", compact('data'));
            } else {
                return redirect()->route('master.expense.index')->with('error', 'Data pengeluaran tidak ditemukan.');
            }
        } catch (\Throwable $th) {
            return redirect()->route('master.expense.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = Expense::find($id);

            $attr = $request->validate([
                'date'          => 'required|date',
                'total_expense' => 'required|string',
                'note'          => 'required|string',
                'file'          => 'mimes:pdf,xlsx,xls,csv,doc,docx,png,jpeg,jpg,gif|max:10000'
            ]);

            if ($data) {
                if ($request->has('file')) {
                    $uuid = Uuid::uuid4();
                    $fileName = Carbon::now()->format('Y-m-d')."_{$uuid}".'.'.$request->file->extension();
                    $request->file->move(public_path('storage/expense_uploads'), $fileName);
                    $relativePath = 'storage/expense_uploads/' . $fileName;

                    if ($data->file_path != null && fileExists(public_path($data->file_path))) {
                        unlink(public_path($data->file_path));
                    }
                } else {
                    $relativePath = $data->file_path;
                }

                $data->update([
                    'date'          => $attr['date'],
                    'total_expense' => $attr['total_expense'],
                    'note'          => $attr['note'],
                    'file_path'     => $relativePath,
                    'status'        => 'pending' // Set status setelah di ubah kembali ke 'pending'
                ]);
            }

            return redirect()->route('master.expense.index')->with('success', 'Data pengeluaran berhasil diubah.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.expense.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.expense.index')->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = Expense::find($id);

            if ($data) {
                if ($data->file_path != null && fileExists(public_path($data->file_path))) {
                    unlink(public_path($data->file_path));
                }

                $data->delete();
            } else  {
                return redirect()->route('master.expense.index')->with('error', 'Data pengeluaran tidak ditemukan.');
            }

            return redirect()->route('master.expense.index')->with('success', 'Data pengeluaran berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->route('master.expense.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    public function approve($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update(['status' => 'approved']);
        return back()->with('success', 'Pengeluaran berhasil di-approve.');
    }

    public function reject($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update(['status' => 'rejected']);
        return back()->with('success', 'Pengeluaran berhasil di-reject.');
    }
}
