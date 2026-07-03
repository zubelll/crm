<?php

namespace App\Http\Controllers;

use App\Models\KontakLayanan;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index()
    {
        $kontaks = KontakLayanan::all();
        return view('kontak.index', compact('kontaks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kontak' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'no_whatsapp' => 'required|string|max:20',
            'pesan_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        KontakLayanan::create($validated);
        return redirect()->route('kontak.index')->with('success', 'Kontak layanan berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $kontak = KontakLayanan::findOrFail($id);

        $validated = $request->validate([
            'nama_kontak' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'no_whatsapp' => 'required|string|max:20',
            'pesan_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $kontak->update($validated);
        return redirect()->route('kontak.index')->with('success', 'Kontak layanan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kontak = KontakLayanan::findOrFail($id);
        $kontak->delete();
        return redirect()->route('kontak.index')->with('success', 'Kontak layanan berhasil dihapus.');
    }

    public function generateLink(string $id)
    {
        // Simple API endpoint if needed
        $kontak = KontakLayanan::findOrFail($id);
        return response()->json($kontak);
    }
}
