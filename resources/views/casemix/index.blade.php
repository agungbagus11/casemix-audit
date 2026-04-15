@extends('layouts.app')

@php
    $riskBadge = function ($risk) {
        return match($risk) {
            'high' => 'bg-rose-100 text-rose-700 border border-rose-200',
            'medium' => 'bg-amber-100 text-amber-700 border border-amber-200',
            'low' => 'bg-blue-100 text-blue-700 border border-blue-200',
            default => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        };
    };

    $stageBadge = function ($stage) {
        return match($stage) {
            'new' => 'bg-blue-100 text-blue-700',
            'ai_coding' => 'bg-violet-100 text-violet-700',
            'auditing' => 'bg-orange-100 text-orange-700',
            'document_check' => 'bg-cyan-100 text-cyan-700',
            'review' => 'bg-amber-100 text-amber-700',
            'done' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-slate-100 text-slate-700',
        };
    };
@endphp

@section('content')
    <div class="mb-8">
        <div class="rounded-[28px] bg-gradient-to-r from-slate-900 via-blue-900 to-emerald-700 px-7 py-7 text-white shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide">
                        CASEMIX · REVIEW · PENDING · VERIFIKASI
                    </div>
                    <h2 class="mt-4 text-3xl md:text-4xl font-extrabold leading-tight">
                        Dashboard operasional klaim yang siap dikembangkan ke alur nyata
                    </h2>
                    <p class="mt-3 max-w-3xl text-sm md:text-base text-slate-100/90">
                        Fokus verifikasi diarahkan ke billing vs CPPT, kronologis vs CPPT, dan kelengkapan berkas vs rekam medis.
                    </p>
                </div>

                <form method="POST" action="{{ route('casemix.sync-mock') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 font-bold text-slate-900 shadow-lg hover:bg-slate-100 transition">
                        Sync Mock Discharge
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-slate-200">
            <div class="text-sm text-slate-500">Total Episode</div>
            <div class="mt-3 text-4xl font-extrabold text-slate-900">{{ $stats['total'] }}</div>
            <div class="mt-2 text-xs text-slate-400">Seluruh kasus yang sudah masuk workbench</div>
        </div>

        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-blue-200">
            <div class="text-sm text-blue-500">Ready Review</div>
            <div class="mt-3 text-4xl font-extrabold text-blue-700">{{ $stats['ready_review'] }}</div>
            <div class="mt-2 text-xs text-blue-400">Kasus siap dicek coder/verifikator</div>
        </div>

        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-amber-200">
            <div class="text-sm text-amber-500">Flagged / Medium+</div>
            <div class="mt-3 text-4xl font-extrabold text-amber-700">{{ $stats['flagged'] + $stats['medium_risk'] }}</div>
            <div class="mt-2 text-xs text-amber-400">Kasus butuh perhatian operasional</div>
        </div>

        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-rose-200">
            <div class="text-sm text-rose-500">High Risk</div>
            <div class="mt-3 text-4xl font-extrabold text-rose-700">{{ $stats['high_risk'] }}</div>
            <div class="mt-2 text-xs text-rose-400">Prioritas audit dan tindak lanjut</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-8">
        <div class="xl:col-span-2 rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Filter Episode</h3>
                    <p class="text-sm text-slate-500">Cari berdasarkan pasien, RM, episode, unit, atau DPJP.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('casemix.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Cari</label>
                    <input type="text" name="q" value="{{ $q }}"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-400"
                           placeholder="Nama pasien / RM / episode / dokter">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Stage</label>
                    <select name="stage" class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        <option value="">Semua</option>
                        @foreach(['new','ai_coding','auditing','document_check','review','done'] as $item)
                            <option value="{{ $item }}" @selected($stage === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Risk</label>
                    <select name="risk" class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        <option value="">Semua</option>
                        @foreach(['clear','low','medium','high'] as $item)
                            <option value="{{ $item }}" @selected($risk === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4 flex flex-wrap gap-2 pt-1">
                    <button type="submit"
                            class="rounded-2xl bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-800 transition">
                        Filter
                    </button>
                    <a href="{{ route('casemix.index') }}"
                       class="rounded-2xl bg-slate-200 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-300 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Fokus Verifikasi</h3>
            <div class="space-y-3 text-sm">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="font-semibold text-slate-800">1. Billing vs CPPT</div>
                    <div class="text-slate-500 mt-1">Pastikan item billing punya dukungan narasi klinis dan tindakan.</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="font-semibold text-slate-800">2. Kronologis vs CPPT</div>
                    <div class="text-slate-500 mt-1">Deteksi mismatch kronologi yang perlu telekonfirmasi dengan admisi.</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="font-semibold text-slate-800">3. Berkas vs RM</div>
                    <div class="text-slate-500 mt-1">Pastikan bundling dokumen verifikasi lengkap sebelum submit/follow up.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[30px] bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-900">Daftar Episode Klaim</h3>
            <p class="text-sm text-slate-500 mt-1">Urutan kasus diprioritaskan berdasarkan risiko dan pembaruan terakhir.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-slate-600">
                        <th class="px-5 py-4 font-semibold">Episode</th>
                        <th class="px-5 py-4 font-semibold">Pasien</th>
                        <th class="px-5 py-4 font-semibold">Unit</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Risk</th>
                        <th class="px-5 py-4 font-semibold">AI</th>
                        <th class="px-5 py-4 font-semibold">Flags</th>
                        <th class="px-5 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($episodes as $episode)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-5 align-top">
                                <div class="font-bold text-slate-900">{{ $episode->episode_no }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $episode->simrs_encounter_id }}</div>
                                <div class="text-xs text-slate-500">{{ $episode->sep_no }}</div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="font-bold text-slate-900">{{ $episode->patient_name }}</div>
                                <div class="text-xs text-slate-500 mt-1">RM: {{ $episode->mrn }}</div>
                                <div class="text-xs text-slate-500">{{ $episode->doctor_name }}</div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="font-semibold text-slate-900">{{ $episode->service_unit }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $episode->care_type }}</div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="flex flex-col gap-2">
                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $stageBadge($episode->processing_stage) }}">
                                        {{ $episode->processing_stage }}
                                    </span>
                                    <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $episode->claim_status }}
                                    </span>
                                    <span class="inline-flex w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        {{ $episode->audit_status }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="text-3xl font-extrabold text-slate-900 leading-none">{{ $episode->risk_score }}</div>
                                <div class="mt-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $riskBadge($episode->risk_level) }}">
                                        {{ $episode->risk_level }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="font-bold text-slate-900">
                                    {{ optional($episode->latestAiResult)->confidence_score ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">{{ $episode->documents_count }} dokumen</div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="font-bold text-slate-900">{{ $episode->audit_flags_count }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $episode->reviews_count }} review logs</div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('casemix.run-mock-ai', $episode->id) }}">
                                        @csrf
                                        <button class="rounded-2xl bg-violet-600 px-4 py-2 text-xs font-bold text-white hover:bg-violet-700 transition">
                                            AI Mock
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('casemix.run-mock-audit', $episode->id) }}">
                                        @csrf
                                        <button class="rounded-2xl bg-amber-500 px-4 py-2 text-xs font-bold text-white hover:bg-amber-600 transition">
                                            Audit Mock
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('casemix.update-review', $episode->id) }}">
                                        @csrf
                                        <button class="rounded-2xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition">
                                            Update Review
                                        </button>
                                    </form>

                                    <a href="{{ route('casemix.show', $episode->id) }}"
                                       class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-slate-500">
                                Belum ada data claim episode.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-100">
            {{ $episodes->links() }}
        </div>
    </div>
@endsection