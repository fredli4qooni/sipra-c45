@extends('layouts.app')

@section('title', 'Pengaturan Profil Akun')
@section('subtitle', 'Kelola informasi identitas pribadi, foto profil, dan keamanan kata sandi Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Profile Header Card -->
    <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5">
        @if($user->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shadow-xs flex-shrink-0">
        @else
            <div class="w-16 h-16 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 font-extrabold text-2xl flex items-center justify-center shadow-xs flex-shrink-0 font-display">
                {{ $user->initials }}
            </div>
        @endif
        <div class="text-center sm:text-left flex-1">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 font-display">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }} • {{ $user->nim_nip ?? 'NIP/NIM belum diisi' }}</p>
                </div>
                <span class="inline-flex self-center sm:self-start items-center px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                    @if($user->isAdmin()) bg-purple-50 text-purple-700 border border-purple-200/60
                    @elseif($user->isProdi()) bg-blue-50 text-blue-700 border border-blue-200/60
                    @elseif($user->isDosenPa()) bg-amber-50 text-amber-700 border border-amber-200/60
                    @else bg-brand-50 text-brand-700 border border-brand-200/60
                    @endif">
                    Peran: {{ $user->role_label }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-2">
                Akun terdaftar sejak {{ $user->created_at->format('d F Y') }} • Status: <span class="text-brand-600 font-bold uppercase">{{ $user->status }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Form Update Profile -->
        <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <i data-lucide="user-pen" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Perbarui Informasi Dasar & Foto</h4>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Foto Profil / Avatar Upload Field -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Foto Profil</label>
                    <div class="flex items-center space-x-3.5">
                        <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-brand-50 border border-slate-200 flex-shrink-0 flex items-center justify-center shadow-xs">
                            <img id="avatar_preview" src="{{ $user->avatar_url ?? '' }}" alt="{{ $user->name }}" class="{{ $user->avatar_url ? '' : 'hidden' }} w-full h-full object-cover">
                            <span id="avatar_fallback" class="{{ $user->avatar_url ? 'hidden' : '' }} text-brand-700 font-extrabold text-lg font-display">
                                {{ $user->initials }}
                            </span>
                        </div>
                        <div class="flex-1 space-y-1">
                            <input 
                                type="file" 
                                id="avatar_input" 
                                name="avatar" 
                                accept="image/jpeg,image/png,image/jpg,image/webp" 
                                onchange="previewAvatar(this)" 
                                class="block w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer bg-slate-50 border border-slate-200 rounded-lg p-1.5 transition"
                            >
                            <div class="flex items-center justify-between text-[11px] text-slate-400">
                                <span>Maks. 2 MB (JPG, PNG, WEBP)</span>
                                @if($user->avatar_url)
                                    <label class="inline-flex items-center space-x-1 cursor-pointer text-rose-600 hover:text-rose-700 font-medium">
                                        <input type="checkbox" name="remove_avatar" value="1" id="remove_avatar_cb" onchange="toggleRemoveAvatar(this)" class="w-3.5 h-3.5 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                        <span>Hapus Foto</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                    @error('avatar') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('name') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('email') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Handphone / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('phone') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-xs transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Change Password -->
        <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <i data-lucide="key-round" class="w-4 h-4 text-amber-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Ubah Kata Sandi</h4>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required placeholder="••••••••" class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        <button type="button" onclick="toggleInputVisibility('current_password', 'icon_current')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition">
                            <i id="icon_current" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('current_password') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru (Min. 8 Karakter)</label>
                    <div class="relative">
                        <input type="password" id="new_password" name="password" required placeholder="••••••••" class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        <button type="button" onclick="toggleInputVisibility('new_password', 'icon_new')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition">
                            <i id="icon_new" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="password_confirmation" required placeholder="••••••••" class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        <button type="button" onclick="toggleInputVisibility('confirm_password', 'icon_confirm')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition">
                            <i id="icon_confirm" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-xs transition">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function previewAvatar(input) {
        const preview = document.getElementById('avatar_preview');
        const fallback = document.getElementById('avatar_fallback');
        const removeCb = document.getElementById('remove_avatar_cb');
        if (removeCb) removeCb.checked = false;

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                fallback.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleRemoveAvatar(checkbox) {
        const preview = document.getElementById('avatar_preview');
        const fallback = document.getElementById('avatar_fallback');
        const input = document.getElementById('avatar_input');
        if (checkbox.checked) {
            input.value = '';
            preview.classList.add('hidden');
            fallback.classList.remove('hidden');
        }
    }

    function toggleInputVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        if (window.lucide) {
            lucide.createIcons();
        }
    }
</script>
@endpush
@endsection
