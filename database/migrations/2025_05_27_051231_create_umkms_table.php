<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('nama_bisnis')->unique(); // UMKM Name (identifier), unique to prevent duplicates
            $table->integer('omzet_penjualan_juta_idr'); // Sales Turnover (Beneficial)
            $table->integer('profitabilitas_persen'); // Profitability (Beneficial)
            $table->integer('skor_kredit'); // Credit Score (Beneficial)
            $table->integer('solvabilitas_der'); // Solvency (Non-Beneficial)
            $table->integer('beban_utang_eksisting_juta_idr_bln'); // Existing Debt Burden (Non-Beneficial)
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};