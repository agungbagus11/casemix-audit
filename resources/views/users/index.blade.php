@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900">Manajemen User</h2>
            <p class="text-slate-500 mt-1">Kelola akun, role, status aktif, dan reset password.</p>
        </div>

        <a href="{{ route('users.create') }}"
           class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-slate-800 transition">
            Tambah User
        </a>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200 mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-600 mb-1">Cari</label>
                <input type="text" name="q" value="{{ $q }}"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                       placeholder="Nama atau email">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Role</label>
                <select name="role" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    <option value="">Semua</option>
                    @foreach(['admin', 'casemix', 'verifier', 'manager'] as $item)
                        <option value="{{ $item }}" @selected($role === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    <option value="">Semua</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="md:col-span-4 flex flex-wrap gap-2">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('users.index') }}"
                   class="rounded-2xl bg-slate-200 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="rounded-[30px] bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-slate-600">
                        <th class="px-5 py-4 font-semibold">Nama</th>
                        <th class="px-5 py-4 font-semibold">Email</th>
                        <th class="px-5 py-4 font-semibold">Role</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-slate-700">{{ $user->email }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                       class="rounded-2xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('users.toggle-active', $user->id) }}">
                                        @csrf
                                        <button type="submit"
                                                class="rounded-2xl {{ $user->is_active ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700' }} px-4 py-2 text-xs font-bold text-white transition">
                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-slate-500">
                                Belum ada data user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
@endsection