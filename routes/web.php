<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UmkmController; // Make sure to import your controller

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route for the root URL, redirects to the analysis page
Route::get('/', function () {
    return redirect()->route('umkms.analysis');
});

// Route to show the form for adding UMKM data (GET request)
// Named 'umkms.create' for easy referencing in views/redirects
Route::get('/umkms/create', [UmkmController::class, 'create'])->name('umkms.create');

// Route to handle form submission and store UMKM data (POST request)
// Named 'umkms.store'
Route::post('/umkms', [UmkmController::class, 'store'])->name('umkms.store');

// Route to display the MOORA analysis and ranking (GET request)
// Named 'umkms.analysis'
Route::get('/umkms/analysis', [UmkmController::class, 'analyze'])->name('umkms.analysis');

Route::resource('umkms', UmkmController::class);