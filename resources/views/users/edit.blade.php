@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('users.index') }}"
           class="inline-flex items-center rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300 transition">
            ← Kembali ke User
        </a>
        <h2 class="mt-4 text-3xl font-extrabold text-slate-900">Edit User</h2>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Data User</h3>

            <form method="POST" action="{{ route('users.update', $user->id) }}" class="grid grid-cols-1 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                    <select name="role" class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                        @foreach(['admin', 'casemix', 'verifier', 'manager'] as $roleItem)
                            <option value="{{ $roleItem }}" @selected(old('role', $user->role) === $roleItem)>{{ $roleItem }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $user->is_active))>
                    User aktif
                </label>

                <div class="flex justify-end">
                    <button type="submit"
                            class="rounded-2xl bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-800 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Reset Password</h3>

            <form method="POST" action="{{ route('users.update-password', $user->id) }}" class="grid grid-cols-1 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
                    <input type="password" name="password"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="rounded-2xl bg-amber-500 px-6 py-3 font-bold text-white hover:bg-amber-600 transition">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection