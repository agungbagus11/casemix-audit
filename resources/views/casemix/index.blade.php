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

    $followUpStatusClass = function ($status) {
        return match($status) {
            'resolved' => 'bg-emerald-100 text-emerald-700',
            'closed' => 'bg-slate-200 text-slate-700',
            'waiting' => 'bg-amber-100 text-amber-700',
            default => 'bg-blue-100 text-blue-700',
        };
    };

    $notificationColor = function ($severity) {
        return match($severity) {
            'high' => 'border-rose-200 bg-rose-50 text-rose-800',
            'medium' => 'border-amber-200 bg-amber-50 text-amber-800',
            default => 'border-blue-200 bg-blue-50 text-blue-800',
        };
    };

    $cardColor = function ($color) {
        return match($color) {
            'rose' => 'border-rose-200 bg-rose-50 text-rose-800',
            'amber' => 'border-amber-200 bg-amber-50 text-amber-800',
            'blue' => 'border-blue-200 bg-blue-50 text-blue-800',
            'violet' => 'border-violet-200 bg-violet-50 text-violet-800',
            default => 'border-slate-200 bg-slate-50 text-slate-800',
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
                        Dashboard manajerial untuk monitoring klaim, mismatch, dan tindak lanjut
                    </h2>
                    <p class="mt-3 max-w-3xl text-sm md:text-base text-slate-100/90">
                        Fokus verifikasi diarahkan ke billing vs CPPT, kronologis vs CPPT, dan kelengkapan berkas vs rekam medis.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    @if(($notifications['total_alerts'] ?? 0) > 0)
                        <div class="rounded-2xl bg-white/15 px-5 py-3">
                            <div class="text-xs uppercase tracking-wide text-slate-100/80">Total Alert</div>
                            <div class="text-3xl font-extrabold">{{ $notifications['total_alerts'] }}</div>
                        </div>
                    @endif

                    @role('admin', 'casemix')
                        <form method="POST" action="{{ route('casemix.sync-mock') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 font-bold text-slate-900 shadow-lg hover:bg-slate-100 transition">
                                Sync Mock Discharge
                            </button>
                        </form>
                    @endrole
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-slate-200">
            <div class="text-sm text-slate-500">Total Episode</div>
            <div class="mt-3 text-4xl font-extrabold text-slate-900">{{ $stats['total'] }}</div>
            <div class="mt-2 text-xs text-slate-400">Seluruh kasus yang masuk workbench</div>
        </div>

        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-blue-200">
            <div class="text-sm text-blue-500">Ready Review</div>
            <div class="mt-3 text-4xl font-extrabold text-blue-700">{{ $stats['ready_review'] }}</div>
            <div class="mt-2 text-xs text-blue-400">Kasus siap dicek coder/verifikator</div>
        </div>

        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-amber-200">
            <div class="text-sm text-amber-500">Follow Up Aktif</div>
            <div class="mt-3 text-4xl font-extrabold text-amber-700">{{ $stats['follow_up_open'] + $stats['follow_up_waiting'] }}</div>
            <div class="mt-2 text-xs text-amber-400">Open + waiting yang masih berjalan</div>
        </div>

        <div class="rounded-[26px] bg-white p-5 shadow-sm border border-rose-200">
            <div class="text-sm text-rose-500">High Risk</div>
            <div class="mt-3 text-4xl font-extrabold text-rose-700">{{ $stats['high_risk'] }}</div>
            <div class="mt-2 text-xs text-rose-400">Kasus prioritas audit dan atensi tinggi</div>
        </div>
    </div>

    @if(!empty($notifications['cards']) || !empty($notifications['items']))
        <div class="mb-8">
            <div class="rounded-[30px] bg-white shadow-sm border border-slate-200 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Notifikasi Internal</h3>
                        <p class="text-sm text-slate-500 mt-1">Prioritas kerja dan hal yang perlu ditindaklanjuti sesuai role login.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                    @foreach($notifications['cards'] as $card)
                        <div class="rounded-[22px] border p-4 {{ $cardColor($card['color']) }}">
                            <div class="text-sm font-semibold">{{ $card['label'] }}</div>
                            <div class="mt-2 text-3xl font-extrabold">{{ $card['count'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                    @forelse($notifications['items'] as $item)
                        <div class="rounded-2xl border p-4 {{ $notificationColor($item['severity']) }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-bold">{{ $item['title'] }}</div>
                                    <div class="text-sm mt-1">{{ $item['description'] }}</div>
                                    @if(!empty($item['meta']))
                                        <div class="text-xs mt-2 opacity-80">{{ $item['meta'] }}</div>
                                    @endif
                                </div>

                                @if(!empty($item['link']))
                                    <a href="{{ $item['link'] }}"
                                       class="inline-flex rounded-xl bg-white/80 px-3 py-2 text-xs font-bold text-slate-800 hover:bg-white transition">
                                        Buka
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Tidak ada notifikasi saat ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">
        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Mismatch & Need Confirmation</h3>
            <div class="h-[320px]">
                <canvas id="mismatchChart"></canvas>
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Status Follow-up</h3>
            <div class="h-[320px]">
                <canvas id="followupStatusChart"></canvas>
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Distribusi Risk Episode</h3>
            <div class="h-[320px]">
                <canvas id="riskDistributionChart"></canvas>
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Aktivitas 7 Hari Terakhir</h3>
            <div class="h-[320px]">
                <canvas id="activityChart"></canvas>
            </div>
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

                    @role('admin', 'casemix', 'manager')
                        <a href="{{ route('casemix.export.episodes', request()->query()) }}"
                           class="rounded-2xl bg-emerald-600 px-5 py-3 font-semibold text-white hover:bg-emerald-700 transition">
                            Export Episode CSV
                        </a>
                        <a href="{{ route('casemix.export.followups') }}"
                           class="rounded-2xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700 transition">
                            Export Follow Up CSV
                        </a>
                        <a href="{{ route('casemix.export.verification-summary') }}"
                           class="rounded-2xl bg-violet-600 px-5 py-3 font-semibold text-white hover:bg-violet-700 transition">
                            Export Summary CSV
                        </a>
                    @endrole
                </div>
            </form>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Ringkasan Follow Up</h3>
            <div class="space-y-3 text-sm">
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                    <div class="font-semibold text-blue-800">Open</div>
                    <div class="text-2xl font-extrabold text-blue-700 mt-1">{{ $stats['follow_up_open'] }}</div>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <div class="font-semibold text-amber-800">Waiting</div>
                    <div class="text-2xl font-extrabold text-amber-700 mt-1">{{ $stats['follow_up_waiting'] }}</div>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="font-semibold text-emerald-800">Resolved/Closed</div>
                    <div class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $stats['follow_up_resolved'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">
        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Prioritas Tinggi</h3>
            <div class="space-y-3">
                @forelse($highPriorityFollowUps as $followUp)
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">{{ $followUp->priority }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $followUpStatusClass($followUp->status) }}">{{ $followUp->status }}</span>
                        </div>
                        <div class="font-bold text-slate-900">{{ $followUp->title }}</div>
                        <div class="text-sm text-slate-600 mt-1">{{ optional($followUp->episode)->patient_name }} · {{ optional($followUp->episode)->episode_no }}</div>
                        <div class="text-xs text-slate-500 mt-2">Target: {{ $followUp->target_unit ?: '-' }}</div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">Belum ada follow-up prioritas tinggi yang aktif.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Follow Up Aktif Terbaru</h3>
            <div class="space-y-3">
                @forelse($activeFollowUps as $followUp)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">{{ $followUp->category }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $followUpStatusClass($followUp->status) }}">{{ $followUp->status }}</span>
                        </div>
                        <div class="font-bold text-slate-900">{{ $followUp->title }}</div>
                        <div class="text-sm text-slate-600 mt-1">{{ optional($followUp->episode)->patient_name }} · {{ optional($followUp->episode)->episode_no }}</div>
                        <div class="text-xs text-slate-500 mt-2">Target: {{ $followUp->target_unit ?: '-' }} · Assigned: {{ $followUp->assigned_to_name ?: '-' }}</div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">Belum ada follow-up aktif.</div>
                @endforelse
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
                        <th class="px-5 py-4 font-semibold">Tracker</th>
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
                                <div class="font-bold text-slate-900">{{ $episode->follow_ups_count }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $episode->verification_items_count }} checklist</div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="flex flex-wrap gap-2">
                                    @role('admin', 'casemix')
                                        <form method="POST" action="{{ route('casemix.run-mock-ai', $episode->id) }}">
                                            @csrf
                                            <button class="rounded-2xl bg-violet-600 px-4 py-2 text-xs font-bold text-white hover:bg-violet-700 transition">
                                                AI Mock
                                            </button>
                                        </form>
                                    @endrole

                                    @role('admin', 'casemix', 'verifier')
                                        <form method="POST" action="{{ route('casemix.run-mock-audit', $episode->id) }}">
                                            @csrf
                                            <button class="rounded-2xl bg-amber-500 px-4 py-2 text-xs font-bold text-white hover:bg-amber-600 transition">
                                                Audit Mock
                                            </button>
                                        </form>
                                    @endrole

                                    @role('admin', 'casemix', 'verifier', 'manager')
                                        <form method="POST" action="{{ route('casemix.update-review', $episode->id) }}">
                                            @csrf
                                            <button class="rounded-2xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition">
                                                Update Review
                                            </button>
                                        </form>
                                    @endrole

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

@push('scripts')
<script>
    const mismatchCtx = document.getElementById('mismatchChart');
    if (mismatchCtx) {
        new Chart(mismatchCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['mismatch']['labels']),
                datasets: [
                    {
                        label: 'Mismatch',
                        data: @json($chartData['mismatch']['mismatch']),
                    },
                    {
                        label: 'Need Confirmation',
                        data: @json($chartData['mismatch']['need_confirmation']),
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }

    const followupCtx = document.getElementById('followupStatusChart');
    if (followupCtx) {
        new Chart(followupCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['followup_status']['labels']),
                datasets: [{
                    data: @json($chartData['followup_status']['values']),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }

    const riskCtx = document.getElementById('riskDistributionChart');
    if (riskCtx) {
        new Chart(riskCtx, {
            type: 'pie',
            data: {
                labels: @json($chartData['risk_distribution']['labels']),
                datasets: [{
                    data: @json($chartData['risk_distribution']['values']),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }

    const activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['activity_7_days']['labels']),
                datasets: [{
                    label: 'Aktivitas',
                    data: @json($chartData['activity_7_days']['values']),
                    fill: false,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }
</script>
@endpush