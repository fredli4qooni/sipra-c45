<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriRisiko extends Model
{
    use HasFactory;

    protected $table = 'kategori_risikos';

    protected $fillable = [
        'kode',
        'nama_risiko',
        'label_badge',
        'warna',
        'icon',
        'deskripsi_singkat',
        'pesan_peringatan',
        'rekomendasi_studi',
        'panduan_konsultasi_pa',
        'template_wa',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'panduan_konsultasi_pa' => 'array',
            'urutan' => 'integer',
        ];
    }

    /**
     * Build dynamic WhatsApp message substituting placeholders
     */
    public function formatWaMessage(Mahasiswa $mahasiswa, ?User $dosen = null, ?DataAkademik $latestAkademik = null): string
    {
        $template = $this->template_wa ?: self::getDefaultPresets()[$this->kode]['template_wa'] ?? '';

        $dosenName = $dosen?->name ?? 'Dosen Pembimbing Akademik';
        $ipk = $latestAkademik ? number_format($latestAkademik->ipk, 2) : '-';
        $kehadiran = $latestAkademik ? number_format($latestAkademik->persentase_kehadiran, 1) . '%' : '-';
        $semester = $latestAkademik?->semester ? 'Semester ' . $latestAkademik->semester : '-';

        $replacements = [
            '{nama}' => $mahasiswa->nama,
            '{nim}' => $mahasiswa->nim,
            '{dosen_pa}' => $dosenName,
            '{status_risiko}' => $this->nama_risiko,
            '{ipk}' => $ipk,
            '{kehadiran}' => $kehadiran,
            '{semester}' => $semester,
            '{prodi}' => 'Sistem Informasi',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Return built-in default presets for the 3 risk levels
     */
    public static function getDefaultPresets(): array
    {
        return [
            'tinggi' => [
                'kode' => 'tinggi',
                'nama_risiko' => 'Risiko Tinggi',
                'label_badge' => 'Perhatian Khusus',
                'warna' => 'rose',
                'icon' => 'alert-triangle',
                'deskripsi_singkat' => 'Mahasiswa terindikasi memiliki kendala evaluasi studi (IPK < 2.75, kehadiran < 75%, atau penumpukan SKS tidak lulus).',
                'pesan_peringatan' => 'Sistem SIPRA-C4.5 mendeteksi bahwa performa akademik Anda terindikasi Risiko Tinggi (terkait IPK di bawah 2.75, kehadiran kurang dari 75%, atau penumpukan SKS tidak lulus). Anda sangat disarankan untuk segera menghubungi Dosen PA Anda untuk menyusun rencana pemulihan studi agar terhindar dari sanksi peringatan akademik atau risiko keterlambatan masa studi.',
                'rekomendasi_studi' => 'Performa akademik Anda terindikasi memiliki risiko keterlambatan studi. Segera hubungi Dosen Pembimbing Akademik (Dosen PA) untuk menyusun rencana perbaikan nilai dan konsultasi khusus.',
                'panduan_konsultasi_pa' => [
                    'Konsultasi Beban SKS Semester Depan: Minta arahan Dosen PA untuk mengambil jumlah SKS yang realistis agar fokus perbaikan nilai maksimal.',
                    'Rencana Perbaikan Mata Kuliah (Remidi): Bawa transkrip nilai dan identifikasi mata kuliah nilai D/E untuk dijadwalkan ulang.',
                    'Komitmen Presensi & Kehadiran: Diskusikan kendala perkuliahan yang dihadapi agar memenuhi syarat presensi minimal ujian (75%).',
                    'Jadwal Pemantauan Berkala: Sepakati jadwal pertemuan konsultasi secara berkala minimal 2 hingga 3 kali selama semester berjalan.',
                ],
                'template_wa' => "Assalamu'alaikum Wr. Wb. Bapak/Ibu {dosen_pa}, perkenalkan saya {nama} (NPM: {nim}), mahasiswa bimbingan akademik Anda di Prodi {prodi}. Sehubungan dengan hasil evaluasi akademik SIPRA-C4.5 (Status: {status_risiko}), saya bermaksud memohon izin dan arahan untuk berkonsultasi mengenai rencana studi saya. Terima kasih.",
                'urutan' => 1,
            ],
            'sedang' => [
                'kode' => 'sedang',
                'nama_risiko' => 'Risiko Sedang',
                'label_badge' => 'Waspada & Pendampingan',
                'warna' => 'amber',
                'icon' => 'alert-circle',
                'deskripsi_singkat' => 'Mahasiswa memiliki indikator performa yang mendekati batas kritis dan membutuhkan pengawalan studi.',
                'pesan_peringatan' => 'Performa akademik Anda berada pada kategori Risiko Sedang (Waspada). Beberapa indikator perlu ditingkatkan agar performa kembali optimal. Segera koordinasikan rencana studi semester depan bersama Dosen PA Anda.',
                'rekomendasi_studi' => 'Terdapat beberapa indikator performa yang perlu ditingkatkan, seperti absensi kehadiran perkuliahan atau perbaikan mata kuliah. Disarankan untuk menjadwalkan sesi konsultasi bimbingan bersama Dosen Pembimbing Akademik (PA) Anda.',
                'panduan_konsultasi_pa' => [
                    'Review Mata Kuliah Prasyarat: Pastikan mata kuliah inti dan prasyarat konsentrasi telah diselesaikan.',
                    'Optimalisasi Pengisian KRS: Diskusikan mata kuliah pilihan yang sesuai dengan peminatan dan kemampuan belajar Anda.',
                    'Pencegahan Presensi Kritis: Pastikan kehadiran perkuliahan selalu di atas 80% untuk mengamankan hak ujian.',
                ],
                'template_wa' => "Assalamu'alaikum Wr. Wb. Bapak/Ibu {dosen_pa}, perkenalkan saya {nama} (NPM: {nim}), mahasiswa bimbingan akademik Anda di Prodi {prodi}. Sehubungan dengan hasil evaluasi akademik SIPRA-C4.5 (Status: {status_risiko}), saya bermaksud memohon izin dan arahan untuk berkonsultasi mengenai rencana studi saya. Terima kasih.",
                'urutan' => 2,
            ],
            'rendah' => [
                'kode' => 'rendah',
                'nama_risiko' => 'Risiko Rendah',
                'label_badge' => 'Aman & Lancar',
                'warna' => 'brand',
                'icon' => 'check-circle-2',
                'deskripsi_singkat' => 'Mahasiswa menunjukkan konsistensi belajar yang baik dan berada di jalur kelulusan tepat waktu.',
                'pesan_peringatan' => 'Selamat! Performa akademik Anda terdeteksi dalam kategori Risiko Rendah (Aman). Pertahankan prestasi dan kedisiplinan Anda. Tetap lakukan konsultasi rutin dengan Dosen PA Anda pada setiap awal semester untuk validasi KRS serta perencanaan kelulusan tepat waktu.',
                'rekomendasi_studi' => 'Performa akademik Anda sangat memuaskan. Anda berada di jalur yang tepat untuk lulus tepat waktu pada semester 8. Disarankan untuk mulai merancang topik proposal skripsi/tugas akhir dan aktif mengikuti kegiatan magang atau konferensi ilmiah.',
                'panduan_konsultasi_pa' => [
                    'Penyusunan Rencana Tugas Akhir / Skripsi: Diskusikan peminatan riset dan topik penelitian skripsi sedini mungkin.',
                    'Program MBKM & Magang Industri: Konsultasikan konversi SKS untuk program magang bersertifikat atau studi independen.',
                    'Sertifikasi Kompetensi: Minta arahan mengenai sertifikasi profesi bidang Sistem Informasi yang relevan dengan dunia kerja.',
                ],
                'template_wa' => "Assalamu'alaikum Wr. Wb. Bapak/Ibu {dosen_pa}, perkenalkan saya {nama} (NPM: {nim}), mahasiswa bimbingan akademik Anda di Prodi {prodi}. Sehubungan dengan hasil evaluasi akademik SIPRA-C4.5 (Status: {status_risiko}), saya bermaksud memohon izin dan arahan untuk berkonsultasi mengenai rencana studi saya. Terima kasih.",
                'urutan' => 3,
            ],
        ];
    }

    /**
     * Find by risk name or code with fallback to default preset
     */
    public static function getByStatus(string $statusName): ?self
    {
        $cleaned = trim($statusName);
        $record = self::where('nama_risiko', $cleaned)
            ->orWhere('kode', strtolower(str_replace('risiko ', '', $cleaned)))
            ->first();

        if ($record) {
            return $record;
        }

        // Return virtual instance from preset
        $kode = match($cleaned) {
            'Risiko Tinggi' => 'tinggi',
            'Risiko Sedang' => 'sedang',
            default => 'rendah',
        };

        $preset = self::getDefaultPresets()[$kode] ?? null;
        if ($preset) {
            return new self($preset);
        }

        return null;
    }
}
