<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = AcademicYear::all();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            return view("pages.year.index", compact('data'));
        } catch (\Throwable $th) {
            return redirect()->route('master.year.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.year.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $attr = $request->validate([
                'year'          => 'required|string',
            ]);

            AcademicYear::create([
                'year'          => $attr['year'],
            ]);

            return redirect()->route('master.year.index')->with('success', 'Data tahun ajaran berhasil disimpan.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.year.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.year.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
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
            $data = AcademicYear::find($id);

            if ($data) {
                return view("pages.year.edit", compact('data'));
            } else {
                return redirect()->route('master.year.index')->with('error', 'Data kelas tidak ditemukan.');
            }
        } catch (\Throwable $th) {
            return redirect()->route('master.year.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = AcademicYear::find($id);

            $attr = $request->validate([
                'year'          => 'required|string',
            ]);

            if ($data) {
                $data->update([
                    'year'          => $attr['year'],
                ]);
            }

            return redirect()->route('master.year.index')->with('success', 'Data tahun ajaran berhasil diubah.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.year.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.year.index')->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = AcademicYear::find($id);

            if ($data) {
                $data->delete();
            } else {
                return redirect()->route('master.year.index')->with('error', 'Data kelas tidak ditemukan.');
            }

            return redirect()->route('master.year.index')->with('success', 'Data tahun ajaran berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->route('master.year.index')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
