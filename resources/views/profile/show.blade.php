@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-3xl font-extrabold text-slate-900">Profil Saya</h2>
        <p class="text-slate-500 mt-1">Kelola identitas akun dan ganti password sendiri.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Data Profil</h3>

            <form method="POST" action="{{ route('profile.update') }}" class="grid grid-cols-1 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                    <input type="text"
                           value="{{ $user->role }}"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-600"
                           disabled>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="rounded-2xl bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-800 transition">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Ganti Password</h3>

            <form method="POST" action="{{ route('profile.password') }}" class="grid grid-cols-1 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password Lama</label>
                    <input type="password"
                           name="current_password"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
                    <input type="password"
                           name="password"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password"
                           name="password_confirmation"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                           required>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="rounded-2xl bg-amber-500 px-6 py-3 font-bold text-white hover:bg-amber-600 transition">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection