@extends('layouts.app')

@php
    $statusColor = function ($status) {
        return match($status) {
            'ready_check' => 'bg-blue-100 text-blue-700 border-blue-200',
            'incomplete' => 'bg-rose-100 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    };
@endphp

@section('content')
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <a href="{{ route('casemix.index') }}"
               class="inline-flex items-center rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300 transition">
                ← Kembali ke Dashboard
            </a>
            <h2 class="mt-4 text-3xl font-extrabold text-slate-900">{{ $episode->episode_no }}</h2>
            <p class="text-slate-500 mt-1">{{ $episode->patient_name }} · {{ $episode->service_unit }} · {{ $episode->doctor_name }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="rounded-full bg-blue-100 px-4 py-2 text-sm font-bold text-blue-700">{{ $episode->processing_stage }}</span>
            <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700">{{ $episode->claim_status }}</span>
            <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-bold text-amber-700">{{ $episode->audit_status }}</span>
            <span class="rounded-full bg-rose-100 px-4 py-2 text-sm font-bold text-rose-700">Risk {{ $episode->risk_score }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Ringkasan Kasus</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><span class="font-semibold">SEP:</span> {{ $episode->sep_no }}</div>
                <div><span class="font-semibold">MRN:</span> {{ $episode->mrn }}</div>
                <div><span class="font-semibold">Encounter:</span> {{ $episode->simrs_encounter_id }}</div>
                <div><span class="font-semibold">Care Type:</span> {{ $episode->care_type }}</div>
                <div><span class="font-semibold">Admission:</span> {{ optional($episode->admission_at)->format('d-m-Y H:i') }}</div>
                <div><span class="font-semibold">Discharge:</span> {{ optional($episode->discharge_at)->format('d-m-Y H:i') }}</div>
                <div><span class="font-semibold">Dokter:</span> {{ $episode->doctor_name }}</div>
                <div><span class="font-semibold">Unit:</span> {{ $episode->service_unit }}</div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">Resume Medis</div>
                    <div class="mt-2 text-sm text-slate-700 leading-6">
                        {{ $resumeText !== '' ? $resumeText : 'Belum ada resume medis.' }}
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">CPPT</div>
                    <div class="mt-2 text-sm text-slate-700 leading-6">
                        {{ $cpptText !== '' ? $cpptText : 'Belum ada CPPT.' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Dokumen & AI</h3>

            <div class="space-y-3">
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-sm text-slate-500">Jumlah Dokumen</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $episode->documents->count() }}</div>
                </div>

                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-sm text-slate-500">Audit Flags</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $episode->auditFlags->count() }}</div>
                </div>

                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-sm text-slate-500">Confidence AI Terakhir</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1">
                        {{ optional($episode->aiResults->first())->confidence_score ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200 mb-6">
        <h3 class="text-xl font-bold text-slate-900 mb-5">Panel Verifikasi Casemix</h3>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($verificationPanels as $panel)
                <div class="rounded-3xl border border-slate-200 p-5 bg-slate-50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="font-bold text-slate-900">{{ $panel['title'] }}</h4>
                            <p class="text-sm text-slate-500 mt-1">{{ $panel['desc'] }}</p>
                        </div>
                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $statusColor($panel['status']) }}">
                            {{ $panel['status'] }}
                        </span>
                    </div>

                    <ul class="mt-4 space-y-2 text-sm text-slate-700">
                        @foreach($panel['points'] as $point)
                            <li class="flex gap-2">
                                <span class="mt-1 h-2 w-2 rounded-full bg-slate-400"></span>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Billing Items</h3>
            <div class="space-y-3">
                @forelse($billingItems as $item)
                    <div class="rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 text-sm text-slate-700">
                        {{ $item }}
                    </div>
                @empty
                    <div class="text-slate-500 text-sm">Belum ada item billing.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Dokumen Verifikasi</h3>
            <div class="space-y-3">
                @forelse($episode->documents as $doc)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $doc->document_type }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $doc->file_name ?: '-' }}</div>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $doc->is_available ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $doc->is_available ? 'Available' : 'Missing' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-500 text-sm">Belum ada dokumen.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-1 rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">AI Results</h3>
            <div class="space-y-3">
                @forelse($episode->aiResults as $ai)
                    <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                        <div class="font-bold text-slate-900">{{ $ai->model_name }} · {{ $ai->prompt_version }}</div>
                        <div class="text-sm text-slate-600 mt-2">{{ $ai->primary_diagnosis_text }}</div>
                        <div class="mt-3 text-sm">
                            Confidence:
                            <span class="font-extrabold text-slate-900">{{ $ai->confidence_score }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-500 text-sm">Belum ada AI results.</div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-1 rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Audit Flags</h3>
            <div class="space-y-3">
                @forelse($episode->auditFlags as $flag)
                    <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">{{ $flag->flag_code }}</span>
                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">{{ $flag->severity }}</span>
                        </div>
                        <div class="font-semibold text-slate-900">{{ $flag->flag_title }}</div>
                        <div class="text-sm text-slate-600 mt-2">{{ $flag->flag_description }}</div>
                    </div>
                @empty
                    <div class="text-slate-500 text-sm">Belum ada audit flags.</div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-1 rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Timeline Review</h3>
            <div class="space-y-3">
                @forelse($episode->reviews as $review)
                    <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                        <div class="font-bold text-slate-900">{{ $review->action_type }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $review->reviewer_name }} ({{ $review->reviewer_role }})</div>
                        <div class="text-sm text-slate-600 mt-2">{{ $review->notes }}</div>
                    </div>
                @empty
                    <div class="text-slate-500 text-sm">Belum ada review logs.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Snapshot JSON</h3>
        <pre class="bg-slate-950 text-slate-100 rounded-3xl p-5 text-xs overflow-auto">{{ json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
@endsection