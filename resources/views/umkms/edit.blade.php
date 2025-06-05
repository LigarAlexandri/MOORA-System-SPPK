<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add UMKM Data - SPPK MOORA</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<nav class="fixed top-0 w-full z-5 bg-green-800 ">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto py-4">
    <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
        <span class="self-center text-2xl font-semibold whitespace-nowrap text-white">Flowbite</span>
    </a>
    <button data-collapse-toggle="navbar-hamburger" type="button" class="inline-flex items-center justify-center p-2 w-10 h-10 text-sm text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-700 :text-white :hover:bg-green-600 :focus:ring-white" aria-controls="navbar-hamburger" aria-expanded="false">
      <span class="sr-only">Open main menu</span>
      <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
      </svg>
    </button>
    <div class="hidden w-full" id="navbar-hamburger">
      <ul class="flex flex-col font-medium mt-4 rounded-lg bg-gray-50 :bg-gray-800 :border-gray-700">
        <li>
          <a href="#" class="block py-2 px-3 text-white bg-blue-700 rounded-sm :bg-blue-600" aria-current="page">Home</a>
        </li>
        <li>
          <a href="#" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 :text-gray-400 :hover:bg-gray-700 :hover:text-white">Services</a>
        </li>
        <li>
          <a href="#" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 :text-gray-400 md::hover:text-white :hover:bg-gray-700 :hover:text-white">Pricing</a>
        </li>
        <li>
          <a href="#" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 :text-gray-400 :hover:bg-gray-700 :hover:text-white">Contact</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<body class="bg-yellow-100 min-h-screen pt-20 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-3xl">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Edit UMKM Data</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <form action="{{ route('umkms.update', $umkm->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="nama_bisnis">Nama Bisnis</label>
            <input type="text" name="nama_bisnis" id="nama_bisnis" value="{{ old('nama_bisnis', $umkm->nama_bisnis) }}"
                class="w-full px-4 py-2 border rounded" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="omzet_penjualan_juta_idr">Omzet Penjualan (Juta IDR/Tahun)</label>
                <input type="number" name="omzet_penjualan_juta_idr" id="omzet_penjualan_juta_idr"
                    value="{{ old('omzet_penjualan_juta_idr', $umkm->omzet_penjualan_juta_idr) }}"
                    class="w-full px-4 py-2 border rounded" required>
            </div>
            <div>
                <label for="profitabilitas_persen">Profitabilitas (%)</label>
                <input type="number" name="profitabilitas_persen" id="profitabilitas_persen"
                    value="{{ old('profitabilitas_persen', $umkm->profitabilitas_persen) }}"
                    class="w-full px-4 py-2 border rounded" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="skor_kredit">Skor Kredit (0-100)</label>
                <input type="number" name="skor_kredit" id="skor_kredit"
                    value="{{ old('skor_kredit', $umkm->skor_kredit) }}"
                    class="w-full px-4 py-2 border rounded" required>
            </div>
            <div>
                <label for="solvabilitas_der">Solvabilitas (DER)</label>
                <input type="number" name="solvabilitas_der" id="solvabilitas_der"
                    value="{{ old('solvabilitas_der', $umkm->solvabilitas_der) }}"
                    class="w-full px-4 py-2 border rounded" required>
            </div>
        </div>

        <div>
            <label for="beban_utang_eksisting_juta_idr_bln">Beban Utang Eksisting (Juta IDR/Bulan)</label>
            <input type="number" name="beban_utang_eksisting_juta_idr_bln" id="beban_utang_eksisting_juta_idr_bln"
                value="{{ old('beban_utang_eksisting_juta_idr_bln', $umkm->beban_utang_eksisting_juta_idr_bln) }}"
                class="w-full px-4 py-2 border rounded" required>
        </div>

            <button type="submit"
                    class="w-full bg-green-800 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:scale-105">
                Edit UMKM
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('umkms.analysis') }}"
               class="text-green-600 hover:text-green-800 font-medium text-sm transition duration-200">
                View All UMKM Rankings
            </a>
        </div>
    </div>
</body>
</html>