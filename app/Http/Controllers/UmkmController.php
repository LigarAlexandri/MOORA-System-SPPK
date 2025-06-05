<?php

namespace App\Http\Controllers;

use App\Models\Umkm; // Import your UMKM Model
use App\Services\MooraService; // Import your MOORA service
use Illuminate\Http\Request; // Import Request for handling form submissions

class UmkmController extends Controller
{
    // Property to hold an instance of MooraService
    protected $mooraService;

    // Constructor for dependency injection: MooraService is automatically provided by Laravel
    public function __construct(MooraService $mooraService)
    {
        $this->mooraService = $mooraService;
    }

    /**
     * Show the form for adding a new UMKM.
     * Corresponds to GET /umkms/create
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('umkms.create');
    }

    public function edit($id)
    {
        // 1. Find the UMKM by ID
        // If not found, it will throw a ModelNotFoundException which Laravel handles automatically
        $umkm = Umkm::findOrFail($id);

        // 2. Return the edit view with the UMKM data
        return view('umkms.edit', compact('umkm'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validate the incoming request data
        $validatedData = $request->validate([
            'nama_bisnis' => 'required|string|max:255|unique:umkms,nama_bisnis,' . $id, // Unique except for current UMKM
            'omzet_penjualan_juta_idr' => 'required|integer|min:0',
            'profitabilitas_persen' => 'required|integer|min:0|max:100',
            'skor_kredit' => 'required|integer|min:0|max:100',
            'solvabilitas_der' => 'required|integer|min:0',
            'beban_utang_eksisting_juta_idr_bln' => 'required|integer|min:0',
        ]);

        // 2. Find the UMKM by ID and update it
        $umkm = Umkm::findOrFail($id);
        $umkm->update($validatedData);

        // 3. Redirect back to the edit form with a success message
        return redirect()->route('umkms.analysis', $id)->with('success', 'UMKM data updated successfully!');
        
    }

    public function destroy($id)
    {
        // 1. Find the UMKM by ID
        $umkm = Umkm::findOrFail($id);

        // 2. Delete the UMKM record
        $umkm->delete();

        // 3. Redirect back to the create form with a success message
        return redirect()->route('umkms.analysis')->with('success', 'UMKM data deleted successfully!');
    }

    /**
     * Store a newly created UMKM in storage.
     * Handles POST /umkms
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        // Ensures data is present, correct type, and within reasonable bounds
        $validatedData = $request->validate([
            'nama_bisnis' => 'required|string|max:255|unique:umkms,nama_bisnis', // Must be unique in 'umkms' table
            'omzet_penjualan_juta_idr' => 'required|integer|min:0',
            'profitabilitas_persen' => 'required|integer|min:0|max:100',
            'skor_kredit' => 'required|integer|min:0|max:100',
            'solvabilitas_der' => 'required|integer|min:0',
            'beban_utang_eksisting_juta_idr_bln' => 'required|integer|min:0',
        ]);

        // 2. Create the UMKM record in the database
        // Uses the Umkm Model to save data to the 'umkms' table
        Umkm::create($validatedData);

        // 3. Redirect back to the form with a success message
        return redirect()->route('umkms.analysis')->with('success', 'UMKM data added successfully!');
    }

    /**
     * Display the MOORA analysis and ranking of all UMKMs.
     * Handles GET /umkms/analysis
     * @return \Illuminate\View\View
     */
    public function analyze()
    {
        // 1. Retrieve all UMKM data from the database
        // Umkm::all() fetches all records. toArray() converts them into a plain array
        // which is suitable for the MooraService calculation.
        $umkms = Umkm::all()->toArray();

        // 2. Perform MOORA calculation using the service
        $rankedUmkms = $this->mooraService->calculateRanking($umkms);

        // 3. Pass the ranked data to the analysis view
        // compact('rankedUmkms') is a shortcut for ['rankedUmkms' => $rankedUmkms]
        return view('umkms.analysis', compact('rankedUmkms'));
    }
}