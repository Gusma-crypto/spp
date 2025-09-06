<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class IncomeController extends Controller
{
 
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Income::all();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            return view("pages.income.index", compact('data'));
        } catch (\Throwable $th) {
            return redirect()->route('master.income.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.income.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $attr = $request->validate([
                'date'          => 'required|date',
                'total_income'  => 'required|string',
                'note'          => 'required|string'
            ]);

            Income::create([
                'date'          => $attr['date'],
                'total_income'  => $attr['total_income'],
                'note'          => $attr['note']
            ]);

            return redirect()->route('master.income.index')->with('success', 'Data pemasukan berhasil disimpan.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.income.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.income.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = Income::find($id);

            if ($data) {
                return view("pages.income.edit", compact('data'));
            } else {
                return redirect()->route('master.income.index')->with('error', 'Data pemasukan tidak ditemukan.');
            }
        } catch (\Throwable $th) {
            return redirect()->route('master.income.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = Income::find($id);

            $attr = $request->validate([
                'date'          => 'required|date',
                'total_income'  => 'required|string',
                'note'          => 'required|string'
            ]);

            if ($data) {
                $data->update([
                    'date'          => $attr['date'],
                    'total_income'  => $attr['total_income'],
                    'note'          => $attr['note']
                ]);
            }

            return redirect()->route('master.income.index')->with('success', 'Data pemasukan berhasil diubah.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.income.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.income.index')->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = Income::find($id);

            if ($data) {
                $data->delete();
            } else  {
                return redirect()->route('master.income.index')->with('error', 'Data pemasukan tidak ditemukan.');
            }

            return redirect()->route('master.income.index')->with('success', 'Data pemasukan berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->route('master.income.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
}
