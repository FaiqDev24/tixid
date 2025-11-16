<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->get();
        return view('admin.user.index', compact('users'));
    }

    public function register(Request $request)
    //Request $request mengambil, memvalidasi, dan memanipulasi semua data dari HTTP yang masuk
    {
        //validasi data argumen pertama untuk memastikan data yang diisi sesuai dengan yang diharapkan dan yang kedua untuk memunculkan pesan kesalahan
        $request->validate([
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email:dns',
            'password' => 'required|min:8',
            'role' => 'user'
        ], [
            'first_name.required' => 'Nama depan harus diisi.',
            'first_name.min' => 'Nama depan harus terdiri dari minimal 3 karakter.',
            'last_name.required' => 'Nama belakang harus diisi.',
            'last_name.min' => 'Nama belakang harus terdiri dari minimal 3 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password harus terdiri dari minimal 8 karakter.',
        ]);

        //create di sini adalah eloquent hash befungsi untuk mengubah karakter menjadi acak namanya adalah enkripsi
        //:: di sebut konsep static di OOP
        //class harus menggunakan pascal case
        $createData = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        //redirect untuk memindahkan/mengalihkan with untuk memberikan pesan dan terkirim dalam bentuk Session
        if ($createData) {
            return redirect()->route('login')->with('success', 'Berhasil membuat akun, silahkan login!');
        } else {
            return redirect()->route('signup')->with('failed', 'Gagal memproses data, silahkan coba lagi!');
        }
    }

    public function authentication(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ], [
            'email.required' => 'Email harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        // data yang akan digunakan untuk verifikasi
        $data = $request->only('email', 'password');
        //Auth::attempt() -> mencocokan data (email-pw)
        if (Auth::attempt($data)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil Login sebagai Admin!');
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.dashboard')->with('success', 'Berhasil Login sebagai Petugas/Staff!');
            } else {
                //jika email-pw cocok
                return redirect()->route('home')->with('success', 'Berhasil Login!');
            }
        } else {
            return redirect()->back()->with('error', 'Gagal Login! pastikan email dan password benar');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Anda telah logout! silahkan login kembali untuk akses legkap');
    }

    public function datatables()
    {
        $users = User::query();
        return DataTables::of($users)->addIndexColumn()->addColumn('role', function ($user) {
            if ($user['role'] == 'admin') {
                return '<span class="badge badge-primary">Admin</span>';
            }
            return '<span class="badge badge-success">Staff</span>';
        })->addColumn('action', function ($user) {
            $btnEdit = '<a href="' . route('admin.users.edit', $user->id) . '" class="btn btn-secondary m-2">Edit</a>';
            $btnDelete = '<form action="' . route('admin.users.delete', $user->id) . '" method="POST">
                            ' . @csrf_field() . @method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger m-2">Hapus</button>
                        </form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2"> ' . $btnEdit . $btnDelete . '</div>';
        })->rawColumns(['role', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            //     'first_name' => 'required|min:3',
            //     'last_name' => 'required|min:3',
            //     'email' => 'required|email:dns|unique:users,email',
            //     'password' => 'required|min:8',
            // ],[
            //     'first_name.required' => 'Nama depan harus diisi.',
            //     'first_name.min' => 'Nama depan harus terdiri dari minimal 3 karakter.',
            //     'last_name.required' => 'Nama belakang harus diisi.',
            //     'last_name.min' => 'Nama belakang harus terdiri dari minimal 3 karakter.',
            //     'email.required' => 'Email harus diisi.',
            //     'email.email' => 'Format email tidak valid.',
            //     'email.unique' => 'Email sudah terdaftar, silahkan gunakan email lain.',
            //     'password.required' => 'Password harus diisi.',
            //     'password.min' => 'Password harus terdiri dari minimal 8 karakter.',
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.min' => 'Nama harus terdiri dari minimal 3 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, silahkan gunakan email lain.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password harus terdiri dari minimal 8 karakter.',
        ]);

        $createData = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);
        if ($createData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil menambahkan data petugas/staff!');
        } else {
            return redirect()->route('admin.users.create')->with('failed', 'Gagal memproses data, silahkan coba lagi!');
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
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email:dns|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.min' => 'Nama harus terdiri dari minimal 3 karakter.',
            'password.min' => 'Password harus terdiri dari minimal 8 karakter.'
        ]);

        $user = User::where('id', $id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Berhasil memperbarui data staff!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('id', $id);
        $user->delete();
        //$user->forceDelete() : untuk menghapus data secara permanen baik ditampilan dan di database.
        return redirect()->route('admin.users.index')->with('success', 'Berhasil menghapus data staff!');
    }

    public function export()
    {
        // nama file yang diunduh
        $fileName = 'data-user.xlsx';
        // proses download
        return Excel::download(new UserExport, $fileName);
    }

    public function trash()
    {
        $userTrash = User::onlyTrashed()->get();
        return view('admin.user.trash', compact('userTrash'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }
}
