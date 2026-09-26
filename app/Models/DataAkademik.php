<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataAkademik extends Model
{
    use HasFactory;

    protected $table = 'data_akademiks';

    protected $fillable = [
        'mahasiswa_id',
        'semester',
        'tahun_akademik',
        'ips',
        'ipk',
        'sks_semester',
        'sks_total',
        'sks_tidak_lulus',
        'persentase_kehadiran',
        'jumlah_kehadiran',
        'total_pertemuan',
        'status_cuti',
        'kategori_ipk',
        'kategori_ips',
        'kategori_sks',
        'kategori_kehadiran',
        'label_risiko_aktual',
        'label_do_aktual',
        'keterangan',
        'status_intervensi',
        'tindakan_intervensi',
        'catatan_intervensi',
        'tanggal_intervensi',
        'dosen_pa_id',
    ];

    protected function casts(): array
    {
        return [
            'ips' => 'decimal:2',
            'ipk' => 'decimal:2',
            'persentase_kehadiran' => 'decimal:2',
            'status_cuti' => 'boolean',
            'tanggal_intervensi' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function dosenPa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_pa_id');
    }

    /**
     * Auto calculate and set categorical binning values based on research thresholds
     */
    public function calculateCategories(): void
    {
        // IPK & IPS thresholds: Rendah (< 2.75), Cukup (2.75 - 3.25), Tinggi (> 3.25)
        $this->kategori_ipk = match (true) {
            $this->ipk < 2.75 => 'Rendah',
            $this->ipk <= 3.25 => 'Cukup',
            default => 'Tinggi',
        };

        $this->kategori_ips = match (true) {
            $this->ips < 2.75 => 'Rendah',
            $this->ips <= 3.25 => 'Cukup',
            default => 'Tinggi',
        };

        // SKS thresholds: Kurang (< 18), Cukup (18 - 21), Sangat Baik (> 21)
        $this->kategori_sks = match (true) {
            $this->sks_semester < 18 => 'Kurang',
            $this->sks_semester <= 21 => 'Cukup',
            default => 'Sangat Baik',
        };

        // Kehadiran thresholds: Kurang (< 75%), Cukup (75% - 85%), Baik (> 85%)
        $this->kategori_kehadiran = match (true) {
            $this->persentase_kehadiran < 75.0 => 'Kurang',
            $this->persentase_kehadiran <= 85.0 => 'Cukup',
            default => 'Baik',
        };
    }
}
