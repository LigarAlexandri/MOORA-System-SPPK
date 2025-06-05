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

<body class="bg-yellow-100 min-h-screen pt-20">
    <div class="container mx-auto  p-8 rounded-lg ">
        {{-- <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">UMKM Loan Eligibility</h1> --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800 text-center md:text-left mb-4 md:mb-0">UMKM Loan Eligibility</h1>
            <a href="{{ route('umkms.create') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-800 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-800 transition duration-300 ease-in-out transform hover:scale-105">
                Add New UMKM
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-10">
            @foreach (['Omzet Penjualan', 'Profitabilitas', 'Skor Kredit', 'Solvabilitas', 'Beban Utang Eksisting'] as $label)
                <div class="bg-yellow-50 border-l-4 border-orange-300 rounded-lg shadow p-4 text-center">
                    <h2 class="text-lg font-semibold mb-2">{{ $label }}</h2>
                    <p class="text-2xl font-bold text-gray-900">0.000</p>
                </div>
            @endforeach
        </div>
        <h1 class="text-3xl font-bold text-gray-800 text-center">Recommendation Ranking (MOORA)</h1>
    </div>
        <div class="container mx-auto bg-white p-8 rounded-lg shadow-xl mb-8">        
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Aksi</th>
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
                                <td class="px-4 py-2">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('umkms.edit', $umkm['id']) }}"
                                        class="px-3 py-1 text-sm font-semibold text-white bg-indigo-600 rounded hover:bg-indigo-700">
                                            Edit
                                        </a>

                                        <form action="{{ route('umkms.destroy', $umkm['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 text-sm font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    {{-- <div class="mt-8 text-center">
        <a href="{{ route('umkms.create') }}"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105">
            Add New UMKM
        </a>
    </div> --}}

</body>
</html>