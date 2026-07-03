<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\CutiRecord;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $pengajuan_terbaru = Pengajuan::with('mahasiswa')->orderBy('created_at', 'desc')->take(5)->get();
        $pengingat_cuti = CutiRecord::with('mahasiswa')
            ->where('status_notif', 'belum')
            ->whereRaw('DATEDIFF(tanggal_selesai, CURDATE()) <= 30')
            ->orderBy('tanggal_selesai', 'asc')
            ->get();
            
        $aktif = Mahasiswa::where('status', 'aktif')->count();
        $cuti = Mahasiswa::where('status', 'cuti')->count();
        $do = Mahasiswa::where('status', 'drop_out')->count();
        $tanpa_ket = Mahasiswa::where('status', 'tanpa_keterangan')->count();

        $stats = [
            'aktif' => ['count' => $aktif, 'change' => '+2.1%', 'dir' => 'up'],
            'cuti' => ['count' => $cuti, 'change' => '+0.5%', 'dir' => 'up'],
            'do' => ['count' => $do, 'change' => '±0%', 'dir' => 'flat'],
            'tanpa_keterangan' => ['count' => $tanpa_ket, 'change' => '-1.2%', 'dir' => 'down'],
        ];

        $recentPengajuan = $pengajuan_terbaru->map(function($p) {
            return [
                'nama' => $p->mahasiswa->nama ?? 'Unknown',
                'nim' => $p->mahasiswa->nim ?? '-',
                'layanan' => $p->jenis_layanan,
                'status' => strtolower($p->status),
                'tanggal' => $p->created_at->format('Y-m-d')
            ];
        });

        $reminders = $pengingat_cuti->map(function($c) {
            return [
                'nama' => $c->mahasiswa->nama ?? 'Unknown',
                'nim' => $c->mahasiswa->nim ?? '-',
                'sisa' => Carbon::parse($c->tanggal_selesai)->diffInDays(now())
            ];
        });

        return view('dashboard.index', compact('stats', 'recentPengajuan', 'reminders'));
    }
}

