<?php

namespace App\Services;

class MooraService
{
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
     * @return array An associative array containing 'ranked_umkms' and 'weights'.
     */
    public function calculateRanking(array $umkmsData): array
    {
        if (empty($umkmsData)) {
            return [
                'ranked_umkms' => [],
                'weights' => []
            ];
        }

        $allCriteria = array_merge($this->beneficialCriteria, $this->nonBeneficialCriteria);

        // Step 1: Normalize the Decision Matrix (Vector Normalization)
        $denominator = $this->calculateDenominators($umkmsData, $allCriteria);
        $normalizedMatrix = $this->normalizeMatrix($umkmsData, $allCriteria, $denominator);

        // Step 2: Calculate Weights using the CRITIC Method
        $weights = $this->calculateCriticWeights($umkmsData, $allCriteria);

        // Step 3: Calculate the MOORA Score (Yi)
        $rankedUmkms = $this->calculateMooraScores($umkmsData, $normalizedMatrix, $weights);

        // Step 4: Rank the Alternatives
        usort($rankedUmkms, function($a, $b) {
            return $b['moora_score'] <=> $a['moora_score'];
        });

        foreach ($rankedUmkms as $key => &$umkm) {
            $umkm['rank'] = $key + 1;
        }

        return [
            'ranked_umkms' => $rankedUmkms,
            'weights' => $weights // Return the calculated weights
        ];
    }

    /**
     * Helper to calculate the square root of the sum of squares for each criterion.
     */
    private function calculateDenominators(array $umkmsData, array $allCriteria): array
    {
        $denominator = [];
        foreach ($allCriteria as $criterion) {
            $sumOfSquares = 0;
            foreach ($umkmsData as $umkm) {
                $sumOfSquares += pow($umkm[$criterion], 2);
            }
            $denominator[$criterion] = sqrt($sumOfSquares);
        }
        return $denominator;
    }

    /**
     * Helper to normalize the decision matrix.
     */
    private function normalizeMatrix(array $umkmsData, array $allCriteria, array $denominator): array
    {
        $normalizedMatrix = [];
        foreach ($umkmsData as $index => $umkm) {
            $normalizedMatrix[$index] = ['nama_bisnis' => $umkm['nama_bisnis']];
            foreach ($allCriteria as $criterion) {
                $normalizedMatrix[$index][$criterion] = ($denominator[$criterion] != 0)
                    ? $umkm[$criterion] / $denominator[$criterion]
                    : 0;
            }
        }
        return $normalizedMatrix;
    }

    /**
     * Helper to calculate MOORA scores.
     */
    private function calculateMooraScores(array $umkmsData, array $normalizedMatrix, array $weights): array
    {
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

            $rankedUmkms[] = array_merge($umkmsData[$index], [
                'moora_score' => round($mooraScore, 4)
            ]);
        }
        return $rankedUmkms;
    }

    /**
     * Calculates the weights for each criterion using the CRITIC method.
     *
     * @param array $umkmsData Original UMKM data.
     * @param array $allCriteria All criteria names.
     * @return array Associative array of criterion weights.
     */
    private function calculateCriticWeights(array $umkmsData, array $allCriteria): array
    {
        $numAlternatives = count($umkmsData);
        $weights = [];
        $informationContent = [];

        // 1. Extract criterion values into arrays
        $criterionValues = [];
        foreach ($allCriteria as $criterion) {
            $criterionValues[$criterion] = array_column($umkmsData, $criterion);
        }

        // 2. Calculate Standard Deviation for each criterion
        $stdDevs = [];
        foreach ($allCriteria as $criterion) {
            $stdDevs[$criterion] = $this->calculateStandardDeviation($criterionValues[$criterion]);
        }

        // 3. Calculate Correlation Coefficient between all pairs of criteria
        $correlationMatrix = [];
        foreach ($allCriteria as $i => $crit1) {
            $correlationMatrix[$crit1] = [];
            foreach ($allCriteria as $j => $crit2) {
                if ($i === $j) {
                    $correlationMatrix[$crit1][$crit2] = 1; // Correlation with itself is 1
                } else {
                    $correlationMatrix[$crit1][$crit2] = $this->calculatePearsonCorrelation(
                        $criterionValues[$crit1],
                        $criterionValues[$crit2]
                    );
                }
            }
        }

        // 4. Calculate Information Content (Cj)
        // Cj = StdDev_j * Sum(1 - r_jk)
        foreach ($allCriteria as $critJ) {
            $sumOfOneMinusCorrelation = 0;
            foreach ($allCriteria as $critK) {
                $sumOfOneMinusCorrelation += (1 - $correlationMatrix[$critJ][$critK]);
            }
            $informationContent[$critJ] = $stdDevs[$critJ] * $sumOfOneMinusCorrelation;
        }

        // 5. Normalize Information Content to get CRITIC Weights
        $sumOfInformationContent = array_sum($informationContent);
        foreach ($allCriteria as $criterion) {
            $weights[$criterion] = ($sumOfInformationContent != 0)
                ? $informationContent[$criterion] / $sumOfInformationContent
                : 0; // Handle division by zero
        }

        return $weights;
    }

    /**
     * Calculates the mean of an array of numbers.
     */
    private function calculateMean(array $numbers): float
    {
        if (empty($numbers)) {
            return 0;
        }
        return array_sum($numbers) / count($numbers);
    }

    /**
     * Calculates the standard deviation of an array of numbers.
     */
    private function calculateStandardDeviation(array $numbers): float
    {
        if (count($numbers) < 2) { // Need at least 2 numbers for standard deviation
            return 0;
        }
        $mean = $this->calculateMean($numbers);
        $sumOfSquaredDifferences = 0;
        foreach ($numbers as $number) {
            $sumOfSquaredDifferences += pow($number - $mean, 2);
        }
        // Using N-1 for sample standard deviation, commonly used in CRITIC
        return sqrt($sumOfSquaredDifferences / (count($numbers) - 1));
    }

    /**
     * Calculates the Pearson correlation coefficient between two arrays.
     */
    private function calculatePearsonCorrelation(array $x, array $y): float
    {
        $n = count($x);
        if ($n !== count($y) || $n < 2) {
            return 0; // Cannot calculate correlation
        }

        $meanX = $this->calculateMean($x);
        $meanY = $this->calculateMean($y);

        $numerator = 0;
        $sumSqDiffX = 0;
        $sumSqDiffY = 0;

        for ($i = 0; $i < $n; $i++) {
            $diffX = $x[$i] - $meanX;
            $diffY = $y[$i] - $meanY;
            $numerator += ($diffX * $diffY);
            $sumSqDiffX += pow($diffX, 2);
            $sumSqDiffY += pow($diffY, 2);
        }

        $denominator = sqrt($sumSqDiffX * $sumSqDiffY);

        return ($denominator != 0) ? $numerator / $denominator : 0;
    }
}