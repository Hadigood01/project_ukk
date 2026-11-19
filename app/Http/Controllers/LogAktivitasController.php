<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;

class LogAktivitasController extends Controller
{
    // Menampilkan log aktivitas
    public function index()
    {
        $logs = LogAktivitas::with('user')
            ->latest('created_at')
            ->paginate(5);

        return view('admin.log.index', compact('logs'));
    }

    // Menghapus satu log aktivitas
    public function destroy($id)
    {
        $log = LogAktivitas::findOrFail($id);

        $log->delete();

        return redirect()
            ->route('admin.log.index')
            ->with('success', 'Log aktivitas berhasil dihapus.');
    }
}
