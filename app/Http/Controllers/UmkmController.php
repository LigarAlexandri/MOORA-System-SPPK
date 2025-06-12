<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Services\MooraService; // Import the MooraService
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    protected $mooraService;

    public function __construct(MooraService $mooraService)
    {
        $this->mooraService = $mooraService;
    }

    public function index()
    {
        $umkms = Umkm::all();
        return view('umkms.index', compact('umkms'));
    }

    public function create()
    {
        return view('umkms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bisnis' => 'required',
            'omzet_penjualan_juta_idr' => 'required|numeric',
            'profitabilitas_persen' => 'required|numeric',
            'solvabilitas_der' => 'required|numeric',
            'beban_utang_eksisting_juta_idr_bln' => 'required|numeric',
            'skor_kredit' => 'required|numeric',
        ]);

        Umkm::create($request->all());

        return redirect()->route('umkms.index')
                         ->with('success', 'UMKM added successfully.');
    }

    public function show(Umkm $umkm)
    {
        return view('umkms.show', compact('umkm'));
    }

    public function edit(Umkm $umkm)
    {
        return view('umkms.edit', compact('umkm'));
    }

    public function update(Request $request, Umkm $umkm)
    {
        $request->validate([
            'nama_bisnis' => 'required',
            'omzet_penjualan_juta_idr' => 'required|numeric',
            'profitabilitas_persen' => 'required|numeric',
            'solvabilitas_der' => 'required|numeric',
            'beban_utang_eksisting_juta_idr_bln' => 'required|numeric',
            'skor_kredit' => 'required|numeric',
        ]);

        $umkm->update($request->all());

        // Redirect ini sekarang akan berfungsi dengan benar
        return redirect()->route('umkms.analysis')
                         ->with('success', 'UMKM updated successfully.');
    }

    public function destroy(Umkm $umkm)
    {
        $umkm->delete();

        return redirect()->route('umkms.index')
                         ->with('success', 'UMKM deleted successfully.');
    }

    public function analyze()
    {
        $umkmsData = Umkm::all()->toArray();

        // Call the MOORA service to get ranked UMKM data and weights
        $result = $this->mooraService->calculateRanking($umkmsData);

        $rankedUmkms = $result['ranked_umkms'];
        $weights = $result['weights']; // Get the weights

        // Pass both to the view
        return view('umkms.analysis', compact('rankedUmkms', 'weights'));
    }
}