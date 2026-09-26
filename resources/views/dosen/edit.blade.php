@extends('layouts.app')

@section('title', 'Ubah Data Dosen PA: ' . $dosen->name)
@section('subtitle', 'Perbarui informasi identitas, kontak, atau kata sandi akun Dosen PA')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="user-pen" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Edit Data Dosen PA</h3>
                    <p class="text-xs text-slate-500">{{ $dosen->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.dosen.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('admin.dosen.update', $dosen) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap Beserta Gelar -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $dosen->name) }}" required placeholder="contoh: Dr. H. Ahmad Fauzi, S.Kom., M.T.I." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                @error('name') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NIP / NIDN -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">NIP / NIDN</label>
                    <input type="text" name="nim_nip" value="{{ old('nim_nip', $dosen->nim_nip) }}" placeholder="contoh: 198503102012011002" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('nim_nip') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- No Telepon / WhatsApp -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Handphone / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $dosen->phone) }}" placeholder="contoh: 081234567890" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('phone') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Email Akun -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Login <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $dosen->email) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('email') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kata Sandi Baru (Opsional) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('password') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Status Akun -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Keaktifan Akun <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="active" {{ old('status', $dosen->status) === 'active' ? 'selected' : '' }}>Aktif (Dapat Login & Membimbing)</option>
                    <option value="inactive" {{ old('status', $dosen->status) === 'inactive' ? 'selected' : '' }}>Non-Aktif (Login Dinonaktifkan)</option>
                </select>
                @error('status') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('admin.dosen.index') }}" class="px-4 py-2.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-xs transition flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-1.5"></i>
                    Perbarui Dosen PA
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
