<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::query();

        // Search logic
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
        }

        // Status logic
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $mahasiswas = $query->latest()->paginate(10);
        $statusCounts = [
            'semua' => Mahasiswa::count(),
            'aktif' => Mahasiswa::where('status', 'aktif')->count(),
            'cuti' => Mahasiswa::where('status', 'cuti')->count(),
            'drop_out' => Mahasiswa::where('status', 'drop_out')->count(),
            'tanpa_keterangan' => Mahasiswa::where('status', 'tanpa_keterangan')->count(),
        ];

        return view('mahasiswa.index', compact('mahasiswas', 'statusCounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|unique:mahasiswas,nim|max:20',
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|string|max:4',
            'semester' => 'required|integer|min:1',
            'status' => 'required|in:aktif,cuti,drop_out,tanpa_keterangan',
            'no_whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'sks_tempuh' => 'required|integer|min:0',
        ]);

        Mahasiswa::create($validated);
        return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'nim' => 'required|max:20|unique:mahasiswas,nim,' . $id,
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|string|max:4',
            'semester' => 'required|integer|min:1',
            'status' => 'required|in:aktif,cuti,drop_out,tanpa_keterangan',
            'no_whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'sks_tempuh' => 'required|integer|min:0',
        ]);

        $mahasiswa->update($validated);
        return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();
        return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa berhasil dihapus.');
    }

    public function byStatus($status)
    {
        return redirect()->route('mahasiswa.index', ['status' => $status]);
    }
}
