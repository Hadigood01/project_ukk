<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan seluruh data pengguna.
     */
    public function index()
    {
        $users = User::orderBy('id_user', 'desc')->paginate(10);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Menampilkan halaman tambah pengguna.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_user' => 'required|string|max:100',
            'email' => 'required|email|unique:user,email',
            'username' => 'required|string|unique:user,username',
            'password' => 'required|string|min:6',
            'level' => 'required|in:admin,petugas,peminjaman',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit pengguna.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.user.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nama_user' => 'required|string|max:100',
            'email' => 'required|email|unique:user,email,' . $id . ',id_user',
            'username' => 'required|string|unique:user,username,' . $id . ',id_user',
            'level' => 'required|in:admin,petugas,peminjaman',
            'password' => 'nullable|string|min:6',
        ]);

        // Mencegah perubahan level akun sendiri.
        if ($user->id_user == auth()->id()) {
            if ($data['level'] !== $user->level) {
                return back()
                    ->withInput()
                    ->with('error', 'Level akun sendiri tidak dapat diubah.');
            }
        }

        // Password hanya diperbarui jika diisi.
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Mencegah penghapusan akun sendiri.
        if ($user->id_user == auth()->id()) {
            return back()->with(
                'error',
                'Tidak bisa menghapus akun sendiri.'
            );
        }

        // Mencegah penghapusan pengguna yang memiliki transaksi.
        if ($user->peminjaman()->exists()) {
            return back()->with(
                'error',
                'User tidak dapat dihapus karena memiliki riwayat peminjaman.'
            );
        }

        $user->delete();

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
