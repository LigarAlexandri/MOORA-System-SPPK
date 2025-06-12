<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Services\MooraService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import a kelas Rule untuk validasi yang lebih kompleks

class UmkmController extends Controller
{
    protected $mooraService;

    public function __construct(MooraService $mooraService)
    {
        $this->mooraService = $mooraService;
    }

    public function index()
    {
        // Mengarahkan ke halaman analisis karena file umkms.index tidak ada
        return redirect()->route('umkms.analysis');
    }

    public function create()
    {
        return view('umkms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Tambahkan aturan 'unique:umkms' untuk memastikan nama_bisnis unik di tabel 'umkms'
            'nama_bisnis' => 'required|unique:umkms,nama_bisnis',
            'omzet_penjualan_juta_idr' => 'required|numeric',
            'profitabilitas_persen' => 'required|numeric',
            'solvabilitas_der' => 'required|numeric',
            'beban_utang_eksisting_juta_idr_bln' => 'required|numeric',
            'skor_kredit' => 'required|numeric',
        ]);

        Umkm::create($request->all());

        return redirect()->route('umkms.analysis')
                         ->with('success', 'UMKM added successfully.');
    }

    public function show(Umkm $umkm)
    {
        // Fungsi ini belum memiliki view, bisa diarahkan ke edit atau analysis
        // Untuk saat ini kita asumsikan tidak digunakan secara aktif
        return redirect()->route('umkms.edit', $umkm->id);
    }

    public function edit(Umkm $umkm)
    {
        return view('umkms.edit', compact('umkm'));
    }

    public function update(Request $request, Umkm $umkm)
    {
        $request->validate([
            // Tambahkan aturan Rule::unique yang mengabaikan ID dari UMKM yang sedang diedit
            'nama_bisnis' => [
                'required',
                Rule::unique('umkms')->ignore($umkm->id),
            ],
            'omzet_penjualan_juta_idr' => 'required|numeric',
            'profitabilitas_persen' => 'required|numeric',
            'solvabilitas_der' => 'required|numeric',
            'beban_utang_eksisting_juta_idr_bln' => 'required|numeric',
            'skor_kredit' => 'required|numeric',
        ]);

        $umkm->update($request->all());

        return redirect()->route('umkms.analysis')
                         ->with('success', 'UMKM updated successfully.');
    }

    public function destroy(Umkm $umkm)
    {
        $umkm->delete();

        return redirect()->route('umkms.analysis')
                         ->with('success', 'UMKM deleted successfully.');
    }

    public function analyze()
    {
        $umkmsData = Umkm::all()->toArray();

        $result = $this->mooraService->calculateRanking($umkmsData);

        $rankedUmkms = $result['ranked_umkms'];
        $weights = $result['weights'];

        return view('umkms.analysis', compact('rankedUmkms', 'weights'));
    }
}