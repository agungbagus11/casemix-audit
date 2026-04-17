@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('users.index') }}"
           class="inline-flex items-center rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300 transition">
            ← Kembali ke User
        </a>
        <h2 class="mt-4 text-3xl font-extrabold text-slate-900">Tambah User</h2>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
        <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                <input type="password" name="password"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                <select name="role" class="w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                    @foreach(['admin', 'casemix', 'verifier', 'manager'] as $roleItem)
                        <option value="{{ $roleItem }}" @selected(old('role') === $roleItem)>{{ $roleItem }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-9">
                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" checked>
                <label class="text-sm font-semibold text-slate-700">Aktif</label>
            </div>

            <div class="xl:col-span-2 flex justify-end">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-800 transition">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
@endsection