<?php

namespace App\Http\Controllers;

use App\Models\CutiRecord;
use App\Models\Mahasiswa;
use App\Models\KontakLayanan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CutiReminderController extends Controller
{
    public function index()
    {
        // Get cuti records that will expire in less than or equal to 30 days
        $today = Carbon::now();
        $thirtyDaysFromNow = Carbon::now()->addDays(30);

        $cutiRecords = CutiRecord::with('mahasiswa')
            ->where('tanggal_selesai', '>=', $today)
            ->where('tanggal_selesai', '<=', $thirtyDaysFromNow)
            ->orderBy('tanggal_selesai', 'asc')
            ->paginate(10);
            
        // Provide the default contact template for WA
        $kontak = KontakLayanan::where('is_active', true)->first();
        $template = $kontak ? $kontak->pesan_template : "Halo {nama_mahasiswa} (NIM: {nim}),\n\nMasa cuti Anda akan berakhir dalam {sisa_hari} hari.\nSegera lapor ke prodi.";

        return view('cuti.reminder', compact('cutiRecords', 'template'));
    }

    public function kirimNotif(Request $request, string $id)
    {
        $cuti = CutiRecord::findOrFail($id);
        $cuti->update(['status_notif' => 'terkirim']);
        
        return redirect()->route('cuti.index')->with('success', 'Status notifikasi berhasil diupdate menjadi Terkirim.');
    }

    public function aktifkanKembali(Request $request, string $id)
    {
        $cuti = CutiRecord::findOrFail($id);
        
        $mhs = $cuti->mahasiswa;
        if($mhs) {
            $mhs->update(['status' => 'aktif']);
        }

        return redirect()->route('cuti.index')->with('success', 'Mahasiswa berhasil diaktifkan kembali.');
    }
}
