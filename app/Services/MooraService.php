<?php

namespace App\Services;

class MooraService
{
    // Define which criteria are beneficial (higher is better) and non-beneficial (lower is better)
    // These correspond to the column names in your database/data array
    private $beneficialCriteria = [
        'omzet_penjualan_juta_idr',
        'profitabilitas_persen',
        'skor_kredit',
    ];

    private $nonBeneficialCriteria = [
        'solvabilitas_der',
        'beban_utang_eksisting_juta_idr_bln',
    ];

    /**
     * Performs the MOORA calculation to rank UMKM alternatives.
     *
     * @param array $umkmsData An array of UMKM data, where each UMKM is an associative array
     * e.g., [['nama_bisnis' => 'UMKM A', 'omzet_penjualan_juta_idr' => 1000, ...], ...]
     * @return array The ranked UMKM data, with 'moora_score' and 'rank' added.
     */
    public function calculateRanking(array $umkmsData): array
    {
        if (empty($umkmsData)) {
            return [];
        }

        // Step 1: Normalize the Decision Matrix (Vector Normalization)
        // Calculate the square root of the sum of squares for each criterion
        $denominator = [];
        foreach (array_merge($this->beneficialCriteria, $this->nonBeneficialCriteria) as $criterion) {
            $sumOfSquares = 0;
            foreach ($umkmsData as $umkm) {
                $sumOfSquares += pow($umkm[$criterion], 2);
            }
            $denominator[$criterion] = sqrt($sumOfSquares);
        }

        // Create the normalized matrix
        $normalizedMatrix = [];
        foreach ($umkmsData as $index => $umkm) {
            $normalizedMatrix[$index] = ['nama_bisnis' => $umkm['nama_bisnis']]; // Keep identifier
            foreach (array_merge($this->beneficialCriteria, $this->nonBeneficialCriteria) as $criterion) {
                // Handle division by zero if a denominator is 0 (e.g., all values for a criterion are 0)
                $normalizedMatrix[$index][$criterion] = ($denominator[$criterion] != 0)
                    ? $umkm[$criterion] / $denominator[$criterion]
                    : 0; // Assign 0 if denominator is 0
            }
        }

        // Step 2: Apply Weights (Simplified: Equal Weights for now)
        // For simplicity in this example, we'll use equal weights.
        // In a real application, you might calculate weights using objective methods like CRITIC
        // or subjective methods like AHP/Direct Weighting.
        $weights = [];
        $totalCriteria = count($this->beneficialCriteria) + count($this->nonBeneficialCriteria);
        $equalWeight = 1 / $totalCriteria;

        foreach (array_merge($this->beneficialCriteria, $this->nonBeneficialCriteria) as $criterion) {
            $weights[$criterion] = $equalWeight;
        }

        // Step 3: Calculate the MOORA Score (Yi)
        // Yi = Sum(Weighted Normalized Beneficial) - Sum(Weighted Normalized Non-Beneficial)
        $rankedUmkms = [];
        foreach ($normalizedMatrix as $index => $normalizedUmkm) {
            $beneficialSum = 0;
            foreach ($this->beneficialCriteria as $criterion) {
                $beneficialSum += $normalizedUmkm[$criterion] * $weights[$criterion];
            }

            $nonBeneficialSum = 0;
            foreach ($this->nonBeneficialCriteria as $criterion) {
                $nonBeneficialSum += $normalizedUmkm[$criterion] * $weights[$criterion];
            }

            $mooraScore = $beneficialSum - $nonBeneficialSum;

            // Merge original UMKM data with its calculated MOORA score for display
            $rankedUmkms[] = array_merge($umkmsData[$index], [
                'moora_score' => round($mooraScore, 4) // Round score for readability
            ]);
        }

        // Step 4: Rank the Alternatives
        // Sort in descending order based on MOORA score (higher score is better)
        usort($rankedUmkms, function($a, $b) {
            return $b['moora_score'] <=> $a['moora_score']; // Spaceship operator for comparison (PHP 7+)
        });

        // Add rank number to each UMKM
        foreach ($rankedUmkms as $key => &$umkm) {
            $umkm['rank'] = $key + 1;
        }

        return $rankedUmkms;
    }
}