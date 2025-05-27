<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM MOORA Ranking - SPPK MOORA</title>
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
<body class="bg-gray-100 min-h-screen p-4">
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-xl">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">UMKM Loan Eligibility Ranking (MOORA)</h1>

        <div class="overflow-x-auto rounded-lg shadow-md">
            @if (empty($rankedUmkms))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-md relative mb-4 text-center" role="alert">
                    No UMKM data available. Please <a href="{{ route('umkms.create') }}" class="font-bold underline">add some UMKM data</a> first.
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Rank</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bisnis</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Omzet Penjualan (Juta IDR)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profitabilitas (%)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor Kredit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solvabilitas (DER)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beban Utang Eksisting (Juta IDR)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">MOORA Score</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($rankedUmkms as $umkm)
                            <tr class="{{ $umkm['rank'] % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $umkm['rank'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold">
                                    {{ $umkm['nama_bisnis'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    Rp {{ number_format($umkm['omzet_penjualan_juta_idr'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $umkm['profitabilitas_persen'] }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $umkm['skor_kredit'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ number_format($umkm['solvabilitas_der'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    Rp {{ number_format($umkm['beban_utang_eksisting_juta_idr_bln'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                    {{ $umkm['moora_score'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('umkms.create') }}"
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105">
                Add New UMKM
            </a>
        </div>
    </div>
</body>
</html>