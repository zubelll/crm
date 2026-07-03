<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuans = Pengajuan::with('mahasiswa')->latest()->paginate(10);
        $mahasiswas = Mahasiswa::all(); // Untuk form dropdown tambah pengajuan
        
        $stats = [
            'total' => Pengajuan::count(),
            'menunggu' => Pengajuan::where('status', 'menunggu')->count(),
            'diproses' => Pengajuan::where('status', 'diproses')->count(),
            'selesai' => Pengajuan::where('status', 'selesai')->count(),
        ];
        
        return view('pengajuan.index', compact('pengajuans', 'mahasiswas', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'jenis_layanan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validated['status'] = 'menunggu';
        
        // Handle file upload if any (skipped for now, assuming simple text)

        Pengajuan::create($validated);
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, string $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,ditolak',
        ]);

        $pengajuan->update($validated);
        return redirect()->route('pengajuan.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
