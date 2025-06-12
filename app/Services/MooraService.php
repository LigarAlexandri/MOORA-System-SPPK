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

        // Calculate Weights using the CRITIC Method (Now follows the flowchart)
        $weights = $this->calculateCriticWeights($umkmsData, $allCriteria);

        // Step for MOORA: Normalize the Decision Matrix using Vector Normalization
        $denominator = $this->calculateDenominators($umkmsData, $allCriteria);
        $normalizedMatrixForMoora = $this->normalizeMatrix($umkmsData, $allCriteria, $denominator);

        // Calculate the MOORA Score (Yi)
        $rankedUmkms = $this->calculateMooraScores($umkmsData, $normalizedMatrixForMoora, $weights);

        // Rank the Alternatives
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
     * Normalizes the data using Min-Max scaling based on criterion type, as per the flowchart.
     * This is used specifically for the CRITIC calculation.
     *
     * @param array $data The original data.
     * @param array $allCriteria List of all criteria.
     * @return array The normalized data.
     */
    private function minMaxNormalize(array $data, array $allCriteria): array
    {
        $normalizedData = $data; // Initialize with original structure

        foreach ($allCriteria as $criterion) {
            $values = array_column($data, $criterion);
            $minVal = min($values);
            $maxVal = max($values);
            $range = $maxVal - $minVal;

            if ($range == 0) {
                foreach ($data as $index => $item) {
                    $normalizedData[$index][$criterion] = 0;
                }
                continue;
            }

            $isBeneficial = in_array($criterion, $this->beneficialCriteria);

            foreach ($data as $index => $item) {
                $value = $item[$criterion];
                if ($isBeneficial) {
                    // Benefit formula: (x_ij - x_j^min) / (x_j^max - x_j^min)
                    $normalizedData[$index][$criterion] = ($value - $minVal) / $range;
                } else {
                    // Cost formula: (x_j^max - x_ij) / (x_j^max - x_j^min)
                    $normalizedData[$index][$criterion] = ($maxVal - $value) / $range;
                }
            }
        }

        return $normalizedData;
    }

    /**
     * Calculates the weights for each criterion using the CRITIC method based on the flowchart.
     *
     * @param array $umkmsData Original UMKM data.
     * @param array $allCriteria All criteria names.
     * @return array Associative array of criterion weights.
     */
    private function calculateCriticWeights(array $umkmsData, array $allCriteria): array
    {
        if (count($umkmsData) < 2) {
            // CRITIC requires at least 2 alternatives to calculate std dev and correlation.
            // Return equal weights if not possible.
            $equalWeight = 1 / count($allCriteria);
            return array_fill_keys($allCriteria, $equalWeight);
        }

        // Step 1 (Flowchart): Normalize the matrix using Min-Max
        $normalizedData = $this->minMaxNormalize($umkmsData, $allCriteria);

        // Step 2 (Flowchart): Calculate Standard Deviation from the normalized matrix
        $stdDevs = [];
        foreach ($allCriteria as $criterion) {
            $normalizedValues = array_column($normalizedData, $criterion);
            $stdDevs[$criterion] = $this->calculateStandardDeviation($normalizedValues);
        }

        // Step 3 (Flowchart): Calculate Correlation Coefficient from the normalized matrix
        $correlationMatrix = [];
        foreach ($allCriteria as $i => $crit1) {
            $normalizedValues1 = array_column($normalizedData, $crit1);
            foreach ($allCriteria as $j => $crit2) {
                if ($i === $j) {
                    $correlationMatrix[$crit1][$crit2] = 1;
                } else {
                    if (!isset($correlationMatrix[$crit1][$crit2])) {
                        $normalizedValues2 = array_column($normalizedData, $crit2);
                        $correlation = $this->calculatePearsonCorrelation($normalizedValues1, $normalizedValues2);
                        $correlationMatrix[$crit1][$crit2] = $correlation;
                        $correlationMatrix[$crit2][$crit1] = $correlation;
                    }
                }
            }
        }

        // Step 4 (Flowchart): Calculate Information Content (Cj)
        $informationContent = [];
        foreach ($allCriteria as $critJ) {
            $sumOfOneMinusCorrelation = 0;
            foreach ($allCriteria as $critK) {
                $sumOfOneMinusCorrelation += (1 - $correlationMatrix[$critJ][$critK]);
            }
            $informationContent[$critJ] = $stdDevs[$critJ] * $sumOfOneMinusCorrelation;
        }

        // Step 5 (Flowchart): Normalize Information Content to get CRITIC Weights
        $weights = [];
        $sumOfInformationContent = array_sum($informationContent);
        if ($sumOfInformationContent == 0) {
            $equalWeight = 1 / count($allCriteria);
            return array_fill_keys($allCriteria, $equalWeight);
        }
        
        foreach ($allCriteria as $criterion) {
            $weights[$criterion] = $informationContent[$criterion] / $sumOfInformationContent;
        }

        return $weights;
    }

    /**
     * Helper to calculate the square root of the sum of squares for each criterion (for MOORA Vector Normalization).
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
     * Helper to normalize the decision matrix (for MOORA Vector Normalization).
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
        if (count($numbers) < 2) {
            return 0;
        }
        $mean = $this->calculateMean($numbers);
        $sumOfSquaredDifferences = 0;
        foreach ($numbers as $number) {
            $sumOfSquaredDifferences += pow($number - $mean, 2);
        }
        return sqrt($sumOfSquaredDifferences / (count($numbers) - 1));
    }

    /**
     * Calculates the Pearson correlation coefficient between two arrays.
     */
    private function calculatePearsonCorrelation(array $x, array $y): float
    {
        $n = count($x);
        if ($n !== count($y) || $n < 2) {
            return 0;
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