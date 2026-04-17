@extends('layouts.app')

@php
    $actionColor = function ($action) {
        return match($action) {
            'verification_item_updated' => 'bg-blue-100 text-blue-700',
            'follow_up_created' => 'bg-emerald-100 text-emerald-700',
            'follow_up_updated' => 'bg-amber-100 text-amber-700',
            'ai_result_saved' => 'bg-violet-100 text-violet-700',
            'audit_flags_saved' => 'bg-rose-100 text-rose-700',
            'workflow_updated', 'dashboard_status_update', 'test_status_update' => 'bg-slate-200 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    };
@endphp

@section('content')
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900">Activity Log</h2>
            <p class="text-slate-500 mt-1">Pantau perubahan verifikasi, follow-up, AI, dan workflow episode.</p>
        </div>

        <a href="{{ route('casemix.index') }}"
           class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-slate-800 transition">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
        <div class="rounded-[24px] bg-white p-5 shadow-sm border border-slate-200">
            <div class="text-sm text-slate-500">Total Aktivitas</div>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['total'] }}</div>
        </div>
        <div class="rounded-[24px] bg-white p-5 shadow-sm border border-blue-200">
            <div class="text-sm text-blue-500">Aktivitas Hari Ini</div>
            <div class="mt-2 text-3xl font-extrabold text-blue-700">{{ $stats['today'] }}</div>
        </div>
        <div class="rounded-[24px] bg-white p-5 shadow-sm border border-amber-200">
            <div class="text-sm text-amber-500">Verification Updates</div>
            <div class="mt-2 text-3xl font-extrabold text-amber-700">{{ $stats['verification_updates'] }}</div>
        </div>
        <div class="rounded-[24px] bg-white p-5 shadow-sm border border-emerald-200">
            <div class="text-sm text-emerald-500">Follow Up Updates</div>
            <div class="mt-2 text-3xl font-extrabold text-emerald-700">{{ $stats['followup_updates'] }}</div>
        </div>
        <div class="rounded-[24px] bg-white p-5 shadow-sm border border-violet-200">
            <div class="text-sm text-violet-500">AI / Audit Updates</div>
            <div class="mt-2 text-3xl font-extrabold text-violet-700">{{ $stats['ai_updates'] }}</div>
        </div>
        <div class="rounded-[24px] bg-white p-5 shadow-sm border border-slate-200">
            <div class="text-sm text-slate-500">Workflow Updates</div>
            <div class="mt-2 text-3xl font-extrabold text-slate-700">{{ $stats['workflow_updates'] }}</div>
        </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200 mb-6">
        <form method="GET" action="{{ route('activity.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-600 mb-1">Cari</label>
                <input type="text" name="q" value="{{ $q }}"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                       placeholder="Pasien / episode / reviewer / notes">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Action Type</label>
                <select name="action_type" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    <option value="">Semua</option>
                    @foreach($actionTypes as $item)
                        <option value="{{ $item }}" @selected($actionType === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Reviewer Role</label>
                <select name="reviewer_role" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    <option value="">Semua</option>
                    @foreach($reviewerRoles as $item)
                        <option value="{{ $item }}" @selected($reviewerRole === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex flex-wrap gap-2">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('activity.index') }}"
                   class="rounded-2xl bg-slate-200 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($logs as $log)
            <div class="rounded-[26px] bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $actionColor($log->action_type) }}">
                                {{ $log->action_type }}
                            </span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                {{ $log->reviewer_role ?: 'unknown' }}
                            </span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                {{ optional($log->created_at)->format('d-m-Y H:i') }}
                            </span>
                        </div>

                        <div class="text-lg font-bold text-slate-900">{{ $log->notes ?: 'Tanpa catatan' }}</div>

                        <div class="mt-2 text-sm text-slate-600">
                            Reviewer:
                            <span class="font-semibold text-slate-800">{{ $log->reviewer_name ?: '-' }}</span>
                        </div>

                        @if($log->episode)
                            <div class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="font-bold text-slate-900">{{ $log->episode->episode_no }} · {{ $log->episode->patient_name }}</div>
                                <div class="text-sm text-slate-600 mt-1">
                                    MRN: {{ $log->episode->mrn }} · Unit: {{ $log->episode->service_unit }}
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('casemix.show', $log->episode->id) }}"
                                       class="inline-flex items-center rounded-2xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                                        Buka Episode
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="xl:w-[360px]">
                        @if($log->new_data_json)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="text-sm font-bold text-slate-900 mb-2">Data Baru</div>
                                <pre class="text-xs text-slate-700 whitespace-pre-wrap break-words">{{ json_encode($log->new_data_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-[26px] bg-white border border-slate-200 shadow-sm p-10 text-center text-slate-500">
                Belum ada activity log.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
@endsection