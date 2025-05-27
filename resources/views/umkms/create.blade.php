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
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Add New UMKM Data</h1>

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

        <form action="{{ route('umkms.store') }}" method="POST" class="space-y-4">
            @csrf <div>
                <label for="nama_bisnis" class="block text-gray-700 text-sm font-medium mb-1">Nama Bisnis</label>
                <input type="text" id="nama_bisnis" name="nama_bisnis" value="{{ old('nama_bisnis') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                       placeholder="e.g., Toko Jaya Abadi" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="omzet_penjualan_juta_idr" class="block text-gray-700 text-sm font-medium mb-1">Omzet Penjualan (Juta IDR/Tahun)</label>
                    <input type="number" id="omzet_penjualan_juta_idr" name="omzet_penjualan_juta_idr" value="{{ old('omzet_penjualan_juta_idr') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="e.g., 1500" required min="0">
                </div>
                <div>
                    <label for="profitabilitas_persen" class="block text-gray-700 text-sm font-medium mb-1">Profitabilitas (%)</label>
                    <input type="number" id="profitabilitas_persen" name="profitabilitas_persen" value="{{ old('profitabilitas_persen') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="e.g., 15" required min="0" max="100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="skor_kredit" class="block text-gray-700 text-sm font-medium mb-1">Skor Kredit (0-100)</label>
                    <input type="number" id="skor_kredit" name="skor_kredit" value="{{ old('skor_kredit') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="e.g., 75" required min="0" max="100">
                </div>
                <div>
                    <label for="solvabilitas_der" class="block text-gray-700 text-sm font-medium mb-1">Solvabilitas (Debt-to-Equity Ratio)</label>
                    <input type="number" step="1" id="solvabilitas_der" name="solvabilitas_der" value="{{ old('solvabilitas_der') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="e.g., 1" required min="0">
                </div>
            </div>

            <div>
                <label for="beban_utang_eksisting_juta_idr_bln" class="block text-gray-700 text-sm font-medium mb-1">Beban Utang Eksisting (Juta IDR/Bulan)</label>
                <input type="number" id="beban_utang_eksisting_juta_idr_bln" name="beban_utang_eksisting_juta_idr_bln" value="{{ old('beban_utang_eksisting_juta_idr_bln') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                       placeholder="e.g., 10" required min="0">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:scale-105">
                Add UMKM
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('umkms.analysis') }}"
               class="text-blue-600 hover:text-blue-800 font-medium text-sm transition duration-200">
                View All UMKM Rankings
            </a>
        </div>
    </div>
</body>
</html>