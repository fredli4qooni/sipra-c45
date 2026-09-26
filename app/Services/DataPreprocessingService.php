<?php

namespace App\Services;

class DataPreprocessingService
{
    /**
     * Categorize IPK (Indeks Prestasi Kumulatif) based on research criteria
     * - Rendah: IPK < 2.75
     * - Cukup: 2.75 <= IPK <= 3.25
     * - Tinggi: IPK > 3.25
     */
    public static function categorizeIpk(float $ipk): string
    {
        if ($ipk < 2.75) {
            return 'Rendah';
        } elseif ($ipk <= 3.25) {
            return 'Cukup';
        }
        return 'Tinggi';
    }

    /**
     * Categorize IPS (Indeks Prestasi Semester) based on research criteria
     * - Rendah: IPS < 2.75
     * - Cukup: 2.75 <= IPS <= 3.25
     * - Tinggi: IPS > 3.25
     */
    public static function categorizeIps(float $ips): string
    {
        if ($ips < 2.75) {
            return 'Rendah';
        } elseif ($ips <= 3.25) {
            return 'Cukup';
        }
        return 'Tinggi';
    }

    /**
     * Categorize SKS Semester taken
     * - Kurang: SKS < 18
     * - Cukup: 18 <= SKS <= 21
     * - Sangat Baik: SKS > 21
     */
    public static function categorizeSks(int $sks): string
    {
        if ($sks < 18) {
            return 'Kurang';
        } elseif ($sks <= 21) {
            return 'Cukup';
        }
        return 'Sangat Baik';
    }

    /**
     * Categorize Lecture Attendance Percentage
     * - Kurang: < 75%
     * - Cukup: 75% - 85%
     * - Baik: > 85%
     */
    public static function categorizeKehadiran(float $kehadiran): string
    {
        if ($kehadiran < 75.0) {
            return 'Kurang';
        } elseif ($kehadiran <= 85.0) {
            return 'Cukup';
        }
        return 'Baik';
    }

    /**
     * Estimate risk label based on rule heuristic (for initial dataset labeling if missing)
     */
    public static function determineHeuristicRisk(float $ipk, float $kehadiran, int $sksTidakLulus, bool $isCuti): string
    {
        if ($isCuti || $ipk < 2.50 || $kehadiran < 70.0 || $sksTidakLulus >= 9) {
            return 'Risiko Tinggi';
        } elseif ($ipk < 3.00 || $kehadiran < 80.0 || $sksTidakLulus > 0) {
            return 'Risiko Sedang';
        }
        return 'Risiko Rendah';
    }

    /**
     * Preprocess a raw array of records for C4.5 algorithm consumption
     */
    public static function preprocessDataset(array $records): array
    {
        $processed = [];

        foreach ($records as $row) {
            $ipk = (float) ($row['ipk'] ?? 0);
            $ips = (float) ($row['ips'] ?? 0);
            $sks = (int) ($row['sks_semester'] ?? 0);
            $kehadiran = (float) ($row['persentase_kehadiran'] ?? 0);
            $cuti = (bool) ($row['status_cuti'] ?? false);
            $sksTidakLulus = (int) ($row['sks_tidak_lulus'] ?? 0);

            $processed[] = [
                'id' => $row['id'] ?? null,
                'nim' => $row['nim'] ?? '',
                'nama' => $row['nama'] ?? '',
                'semester' => $row['semester'] ?? 1,
                'kategori_ipk' => self::categorizeIpk($ipk),
                'kategori_ips' => self::categorizeIps($ips),
                'kategori_sks' => self::categorizeSks($sks),
                'kategori_kehadiran' => self::categorizeKehadiran($kehadiran),
                'status_cuti' => $cuti ? 'Ya' : 'Tidak',
                'sks_tidak_lulus' => $sksTidakLulus > 0 ? 'Ada' : 'Tidak Ada',
                'label_risiko_aktual' => $row['label_risiko_aktual'] ?? self::determineHeuristicRisk($ipk, $kehadiran, $sksTidakLulus, $cuti),
                'target' => $row['label_risiko_aktual'] ?? self::determineHeuristicRisk($ipk, $kehadiran, $sksTidakLulus, $cuti),
            ];
        }

        return $processed;
    }
}
