@extends('layouts.app')

@php
    $panelStatusClass = function ($status) {
        return match($status) {
            'match' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'mismatch' => 'bg-rose-100 text-rose-700 border-rose-200',
            'need_confirmation' => 'bg-amber-100 text-amber-700 border-amber-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
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

    $priorityClass = function ($priority) {
        return match($priority) {
            'high' => 'bg-rose-100 text-rose-700',
            'medium' => 'bg-amber-100 text-amber-700',
            default => 'bg-blue-100 text-blue-700',
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
            <h3 class="text-xl font-bold text-slate-900 mb-4">Referensi Operasional</h3>

            <div class="space-y-3">
                <a href="{{ data_get($operationalLinks, 'rm_checklist_sheet') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="block rounded-2xl border border-emerald-200 bg-emerald-50 p-4 hover:bg-emerald-100 transition">
                    <div class="font-bold text-emerald-800">Buka Sheet Kelengkapan Berkas vs RM</div>
                    <div class="text-sm text-emerald-700 mt-1">Referensi checklist eksternal untuk verifikasi dokumen.</div>
                </a>

                @foreach(data_get($operationalLinks, 'notes', []) as $note)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        {{ $note }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200 mb-6">
        <h3 class="text-xl font-bold text-slate-900 mb-5">Import Data Operasional</h3>
        <p class="text-sm text-slate-500 mb-4">
            Paste data dari Excel / Google Sheet dalam format TSV atau CSV dengan header:
            <span class="font-semibold">verification_key, status, finding_notes, follow_up_notes, source_reference, target_unit, priority, title</span>
        </p>

        <form method="POST" action="{{ route('casemix.import-operational', $episode->id) }}" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            @csrf

            <div class="xl:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Data Import</label>
                <textarea name="raw_import_text"
                          rows="10"
                          class="w-full rounded-2xl border border-slate-300 px-4 py-3 font-mono text-sm"
                          placeholder="verification_key	status	finding_notes	follow_up_notes	source_reference	target_unit	priority	title&#10;billing_vs_cppt	mismatch	Item billing ventilator belum jelas di CPPT	Konfirmasi ke unit billing dan cek ulang CPPT	Sheet RM row 12	billing	high	Konfirmasi billing ventilator">{{ old('raw_import_text') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Importer</label>
                <input type="text"
                       name="import_reviewer_name"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                       placeholder="Nama petugas import">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Role Importer</label>
                <input type="text"
                       name="import_reviewer_role"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                       placeholder="casemix / rm / verifier">
            </div>

            <div class="xl:col-span-2 flex justify-end">
                <button type="submit"
                        class="rounded-2xl bg-violet-700 px-6 py-3 font-bold text-white hover:bg-violet-800 transition">
                    Import Operasional
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200 mb-6">
        <h3 class="text-xl font-bold text-slate-900 mb-5">Checklist Verifikasi Casemix</h3>

        <div class="grid grid-cols-1 gap-5">
            @foreach($episode->verificationItems as $item)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-slate-900">{{ $item->verification_label }}</h4>
                            <p class="text-sm text-slate-500 mt-1">Key: {{ $item->verification_key }}</p>
                        </div>

                        <span class="inline-flex w-fit rounded-full border px-4 py-2 text-sm font-bold {{ $panelStatusClass($item->status) }}">
                            {{ $item->status }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('casemix.save-verification', [$episode->id, $item->verification_key]) }}" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        @csrf

                        <input type="hidden" name="verification_label" value="{{ $item->verification_label }}">

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                            <select name="status" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                                @foreach(['not_checked', 'match', 'mismatch', 'need_confirmation'] as $status)
                                    <option value="{{ $status }}" @selected($item->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Sumber Referensi</label>
                            <input type="text"
                                   name="source_reference"
                                   value="{{ old('source_reference', $item->source_reference) }}"
                                   placeholder="CPPT / Billing / Form Kronologis / RM / Google Sheet"
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                        </div>

                        <div class="xl:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Temuan</label>
                            <textarea name="finding_notes"
                                      rows="3"
                                      class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                                      placeholder="Tuliskan hasil pengecekan, mismatch, atau poin yang ditemukan...">{{ old('finding_notes', $item->finding_notes) }}</textarea>
                        </div>

                        <div class="xl:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tindak Lanjut</label>
                            <textarea name="follow_up_notes"
                                      rows="3"
                                      class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                                      placeholder="Contoh: konfirmasi ke admisi, koordinasi RM, cek billing ulang...">{{ old('follow_up_notes', $item->follow_up_notes) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Reviewer</label>
                            <input type="text"
                                   name="reviewer_name"
                                   value="{{ old('reviewer_name', $item->reviewer_name) }}"
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                                   placeholder="Nama reviewer">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Role Reviewer</label>
                            <input type="text"
                                   name="reviewer_role"
                                   value="{{ old('reviewer_role', $item->reviewer_role) }}"
                                   class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                                   placeholder="casemix / RM / admisi / verifier">
                        </div>

                        <div class="xl:col-span-2 flex justify-end">
                            <button type="submit"
                                    class="rounded-2xl bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-800 transition">
                                Simpan Verifikasi
                            </button>
                        </div>
                    </form>
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

    <div class="rounded-[28px] bg-white p-6 shadow-sm border border-slate-200 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Pending / Dispute Tracker</h3>
                <p class="text-sm text-slate-500 mt-1">Catat koordinasi ke admisi, RM, DPJP, atau tindak lanjut casemix.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('casemix.create-follow-up', $episode->id) }}" class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                <select name="category" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    @foreach(['billing', 'chronology', 'documents', 'coding', 'pending', 'dispute'] as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Target Unit</label>
                <select name="target_unit" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    @foreach(['admisi', 'rm', 'dpjp', 'casemix', 'billing', 'verifikator'] as $target)
                        <option value="{{ $target }}">{{ $target }}</option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Follow Up</label>
                <input type="text" name="title" class="w-full rounded-2xl border border-slate-300 px-4 py-3" placeholder="Contoh: Konfirmasi kronologis masuk dengan admisi">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Prioritas</label>
                <select name="priority" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    @foreach(['low', 'medium', 'high'] as $priority)
                        <option value="{{ $priority }}">{{ $priority }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                    @foreach(['open', 'waiting', 'resolved', 'closed'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Issue Summary</label>
                <textarea name="issue_summary" rows="3" class="w-full rounded-2xl border border-slate-300 px-4 py-3" placeholder="Jelaskan masalah singkat yang harus ditindaklanjuti..."></textarea>
            </div>

            <div class="xl:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Action Needed</label>
                <textarea name="action_needed" rows="3" class="w-full rounded-2xl border border-slate-300 px-4 py-3" placeholder="Tindakan yang perlu dilakukan oleh unit terkait..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Dibuat Oleh</label>
                <input type="text" name="created_by_name" class="w-full rounded-2xl border border-slate-300 px-4 py-3" placeholder="Nama petugas">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Assigned To</label>
                <input type="text" name="assigned_to_name" class="w-full rounded-2xl border border-slate-300 px-4 py-3" placeholder="Nama/unit penanggung jawab">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Due Date</label>
                <input type="datetime-local" name="due_at" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
            </div>

            <div class="xl:col-span-2 flex justify-end">
                <button type="submit" class="rounded-2xl bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-800 transition">
                    Tambah Follow Up
                </button>
            </div>
        </form>

        <div class="space-y-4">
            @forelse($episode->followUps as $followUp)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                        <div>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="rounded-full px-3 py-1 text-xs font-bold bg-slate-900 text-white">{{ $followUp->category }}</span>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $priorityClass($followUp->priority) }}">{{ $followUp->priority }}</span>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $followUpStatusClass($followUp->status) }}">{{ $followUp->status }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900">{{ $followUp->title }}</h4>
                            <p class="text-sm text-slate-500 mt-1">Target: {{ $followUp->target_unit ?: '-' }} · Assigned: {{ $followUp->assigned_to_name ?: '-' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('casemix.update-follow-up', [$episode->id, $followUp->id]) }}" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                            <select name="status" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                                @foreach(['open', 'waiting', 'resolved', 'closed'] as $status)
                                    <option value="{{ $status }}" @selected($followUp->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Assigned To</label>
                            <input type="text" name="assigned_to_name" value="{{ $followUp->assigned_to_name }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3">
                        </div>

                        <div class="xl:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Issue Summary</label>
                            <textarea name="issue_summary" rows="2" class="w-full rounded-2xl border border-slate-300 px-4 py-3">{{ $followUp->issue_summary }}</textarea>
                        </div>

                        <div class="xl:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Action Needed</label>
                            <textarea name="action_needed" rows="2" class="w-full rounded-2xl border border-slate-300 px-4 py-3">{{ $followUp->action_needed }}</textarea>
                        </div>

                        <div class="xl:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Resolution Notes</label>
                            <textarea name="resolution_notes" rows="2" class="w-full rounded-2xl border border-slate-300 px-4 py-3">{{ $followUp->resolution_notes }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Updated By</label>
                            <input type="text" name="updated_by_name" class="w-full rounded-2xl border border-slate-300 px-4 py-3" placeholder="Nama petugas update">
                        </div>

                        <div class="xl:col-span-2 flex justify-end">
                            <button type="submit" class="rounded-2xl bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-800 transition">
                                Update Follow Up
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="text-sm text-slate-500">Belum ada follow up / pending tracker untuk episode ini.</div>
            @endforelse
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