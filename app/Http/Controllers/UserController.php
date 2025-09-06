<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = User::selectRaw("users.*, roles.name AS role_name")
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', '!=', 'Siswa')
                ->where('roles.name', '!=', 'Super Admin')
                ->get();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            return view("pages.user.index", compact('data'));
        } catch (\Throwable $th) {
            return redirect()->route('master.user.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $role = Role::where('name', '!=', 'Super Admin')
                ->where('name', '!=', 'Siswa')
                ->get();

            return view("pages.user.create", compact('role'));
        } catch (\Throwable $th) {
            return redirect()->route('master.user.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $attr = $request->validate([
                'role_id'           => 'required|string',
                'first_name'        => 'required|string',
                'last_name'         => 'required|string',
                'gender'            => 'required|string',
                'email'             => 'required|email',
                'password'          => 'required|string',
                'phone'             => 'required|string',
                'address'           => 'required|string'
            ]);

            User::create([
                'role_id'           => $attr['role_id'],
                'first_name'        => $attr['first_name'],
                'last_name'         => $attr['last_name'],
                'gender'            => $attr['gender'],
                'email'             => $attr['email'],
                'password'          => Hash::make($attr['password']),
                'phone'             => $attr['phone'],
                'address'           => $attr['address']
            ]);

            return redirect()->route('master.user.index')->with('success', 'Data pengguna aplikasi berhasil disimpan.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.user.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.user.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
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

            $role = Role::where('name', '!=', 'Super Admin')
                ->where('name', '!=', 'Siswa')
                ->get();

            if ($data) {
                return view("pages.user.edit", compact('role', 'data'));
            } else {
                return redirect()->route('master.user.index')->with('error', 'Data pengguna aplikasi tidak ditemukan.');
            }
        } catch (\Throwable $th) {
            return redirect()->route('master.user.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = User::find($id);

            $attr = $request->validate([
                'role_id'           => 'required|string',
                'first_name'        => 'required|string',
                'last_name'         => 'required|string',
                'gender'            => 'required|string',
                'email'             => 'required|email',
                'phone'             => 'required|string',
                'address'           => 'required|string'
            ]);

            if ($data) {
                $data->update([
                    'role_id'           => $attr['role_id'],
                    'first_name'        => $attr['first_name'],
                    'last_name'         => $attr['last_name'],
                    'gender'            => $attr['gender'],
                    'email'             => $attr['email'],
                    'phone'             => $attr['phone'],
                    'address'           => $attr['address']
                ]);
            }

            return redirect()->route('master.user.index')->with('success', 'Data pengguna aplikasi berhasil diubah.');
        }  catch (ValidationException $e) {
            return redirect()->route('master.user.index')->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('master.user.index')->with('error', 'Terjadi kesalahan saat mengubah data.');
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
                return redirect()->route('master.user.index')->with('error', 'Data pengguna aplikasi tidak ditemukan.');
            }

            return redirect()->route('master.user.index')->with('success', 'Data pengguna aplikasi berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->route('master.user.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
}
