<?php

namespace App\Http\Controllers;

use App\Models\KategoriRisiko;
use Illuminate\Http\Request;

class KategoriRisikoController extends Controller
{
    /**
     * Display listing of risk categories and their configured warning texts
     */
    public function index()
    {
        $kategoriRisikos = KategoriRisiko::orderBy('urutan', 'asc')->get();

        return view('risiko.index', compact('kategoriRisikos'));
    }

    /**
     * Show form for editing specific risk warning and recommendation texts
     */
    public function edit(KategoriRisiko $risiko)
    {
        $defaultPreset = KategoriRisiko::getDefaultPresets()[$risiko->kode] ?? null;

        // Convert array of guidance to newline string for easy editing
        $panduanText = is_array($risiko->panduan_konsultasi_pa)
            ? implode("\n", $risiko->panduan_konsultasi_pa)
            : ($risiko->panduan_konsultasi_pa ?? '');

        return view('risiko.edit', compact('risiko', 'defaultPreset', 'panduanText'));
    }

    /**
     * Update the configured warning texts and guidance for the specified risk
     */
    public function update(Request $request, KategoriRisiko $risiko)
    {
        $validated = $request->validate([
            'label_badge' => ['required', 'string', 'max:100'],
            'deskripsi_singkat' => ['nullable', 'string', 'max:500'],
            'pesan_peringatan' => ['required', 'string', 'max:2500'],
            'rekomendasi_studi' => ['required', 'string', 'max:2500'],
            'panduan_konsultasi_pa' => ['nullable', 'string'],
            'template_wa' => ['nullable', 'string', 'max:2500'],
        ], [
            'label_badge.required' => 'Label badge kategori risiko wajib diisi.',
            'pesan_peringatan.required' => 'Teks pesan peringatan wajib diisi.',
            'rekomendasi_studi.required' => 'Teks rekomendasi rencana studi wajib diisi.',
        ]);

        // Parse multiline guidance into clean array
        $rawPanduan = $request->input('panduan_konsultasi_pa', '');
        $panduanLines = array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r", "", $rawPanduan))),
            fn($line) => $line !== ''
        ));

        $risiko->update([
            'label_badge' => $validated['label_badge'],
            'deskripsi_singkat' => $validated['deskripsi_singkat'] ?? null,
            'pesan_peringatan' => $validated['pesan_peringatan'],
            'rekomendasi_studi' => $validated['rekomendasi_studi'],
            'panduan_konsultasi_pa' => $panduanLines,
            'template_wa' => $validated['template_wa'] ?? null,
        ]);

        return redirect()->route('admin.risiko.index')
            ->with('success', "Konfigurasi teks peringatan dan rekomendasi untuk {$risiko->nama_risiko} berhasil diperbarui!");
    }

    /**
     * Reset risk warning texts back to standard factory default
     */
    public function reset(KategoriRisiko $risiko)
    {
        $presets = KategoriRisiko::getDefaultPresets();
        $preset = $presets[$risiko->kode] ?? null;

        if (!$preset) {
            return redirect()->route('admin.risiko.index')->with('error', 'Preset default tidak ditemukan.');
        }

        $risiko->update([
            'label_badge' => $preset['label_badge'],
            'deskripsi_singkat' => $preset['deskripsi_singkat'],
            'pesan_peringatan' => $preset['pesan_peringatan'],
            'rekomendasi_studi' => $preset['rekomendasi_studi'],
            'panduan_konsultasi_pa' => $preset['panduan_konsultasi_pa'],
            'template_wa' => $preset['template_wa'],
        ]);

        return redirect()->route('admin.risiko.index')
            ->with('success', "Teks peringatan dan rekomendasi untuk {$risiko->nama_risiko} berhasil dikembalikan ke standar default!");
    }
}
