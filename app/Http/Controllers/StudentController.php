<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\MClass;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $class = urldecode($request->query("class"));

            $classes = MClass::all();

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

            return view("pages.student.index", compact('data', 'classes', 'class'));
        } catch (\Throwable $th) {
            return redirect()->route('master.student.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $years = AcademicYear::all();
            $classes = MClass::all();

            return view("pages.student.create", compact('years', 'classes'));
        } catch (\Throwable $th) {
            return redirect()->route('master.student.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $student_role = Role::where('name', '=', 'Siswa')->first();

            $attr = $request->validate([
                'class_id'          => 'required|string',
                'academic_year_id'  => 'required|string',
                'nisn'              => 'required|string',
                'first_name'        => 'required|string',
                'last_name'         => 'required|string',
                'gender'            => 'required|string',
                'parent_status'     => 'required|string',
                'parent_name'       => 'required|string',
                'email'             => 'required|email',
                'password'          => 'required|string',
                'phone'             => 'required|string',
                'address'           => 'required|string'
            ]);

            User::create([
                'role_id'           => $student_role->id,
                'class_id'          => $attr['class_id'],
                'academic_year_id'  => $attr['academic_year_id'],
                'nisn'              => $attr['nisn'],
                'first_name'        => $attr['first_name'],
                'last_name'         => $attr['last_name'],
                'gender'            => $attr['gender'],
                'parent_status'     => $attr['parent_status'],
                'parent_name'       => $attr['parent_name'],
                'email'             => $attr['email'],
                'password'          => Hash::make($attr['password']),
                'phone'             => $attr['phone'],
                'address'           => $attr['address']
            ]);

            return redirect()->route('master.student.index')->with('success', 'Data siswa berhasil disimpan.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.student.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.student.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
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
            $data = User::find($id);
            $years = AcademicYear::all();
            $classes = MClass::all();

            if ($data) {
                return view("pages.student.edit", compact('years', 'classes', 'data'));
            } else {
                return redirect()->route('master.student.index')->with('error', 'Data siswa tidak ditemukan.');
            }
        } catch (\Throwable $th) {
            return redirect()->route('master.student.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = User::find($id);
            $student_role = Role::where('name', '=', 'Siswa')->first();

            $attr = $request->validate([
                'class_id'          => 'required|string',
                'academic_year_id'  => 'required|string',
                'nisn'              => 'required|string',
                'first_name'        => 'required|string',
                'last_name'         => 'required|string',
                'gender'            => 'required|string',
                'parent_status'     => 'required|string',
                'parent_name'       => 'required|string',
                'email'             => 'required|email',
                'phone'             => 'required|string',
                'address'           => 'required|string'
            ]);

            if ($data) {
                $data->update([
                    'role_id'           => $student_role->id,
                    'class_id'          => $attr['class_id'],
                    'academic_year_id'  => $attr['academic_year_id'],
                    'nisn'              => $attr['nisn'],
                    'first_name'        => $attr['first_name'],
                    'last_name'         => $attr['last_name'],
                    'gender'            => $attr['gender'],
                    'parent_status'     => $attr['parent_status'],
                    'parent_name'       => $attr['parent_name'],
                    'email'             => $attr['email'],
                    'phone'             => $attr['phone'],
                    'address'           => $attr['address']
                ]);
            } else {
                return redirect()->route('master.student.index')->with('error', 'Data siswa tidak ditemukan.');
            }

            return redirect()->route('master.student.index')->with('success', 'Data siswa berhasil diubah.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.student.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.student.index')->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = User::find($id);

            if ($data) {
                $data->delete();
            } else {
                return redirect()->route('master.student.index')->with('error', 'Data siswa tidak ditemukan.');
            }

            return redirect()->route('master.student.index')->with('success', 'Data siswa berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->route('master.student.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
}
