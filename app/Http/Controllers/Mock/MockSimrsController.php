<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MockSimrsController extends Controller
{
    public function discharge(Request $request): JsonResponse
    {
        $date = $request->get('date', now()->toDateString());

        $data = [
            [
                'encounter_id' => 'RJ-458899',
                'no_rm' => 'RM001122',
                'nama_pasien' => 'I Made Sudarma',
                'tanggal_pulang' => $date . ' 14:35:00',
                'unit' => 'ICU',
                'dokter' => 'dr. Ketut Arimbawa, Sp.PD',
            ],
            [
                'encounter_id' => 'RI-458900',
                'no_rm' => 'RM001123',
                'nama_pasien' => 'Ni Luh Sari Dewi',
                'tanggal_pulang' => $date . ' 11:20:00',
                'unit' => 'Rawat Inap Bedah',
                'dokter' => 'dr. Komang Yasa, Sp.B',
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Mock discharge data.',
            'data' => $data,
        ]);
    }

    public function encounterDetail(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => [
                'episode_no' => $episode['episode_no'],
                'encounter_id' => $episode['encounter_id'],
                'patient' => [
                    'no_rm' => $episode['patient']['mrn'],
                    'nama' => $episode['patient']['name'],
                    'jenis_kelamin' => $episode['patient']['gender'],
                    'tanggal_lahir' => $episode['patient']['dob'],
                ],
                'jenis_rawat' => $episode['service']['care_type'],
                'unit' => $episode['service']['unit'],
                'doctor_name' => $episode['service']['doctor'],
                'admission_date' => $episode['service']['admission_date'],
                'discharge_date' => $episode['service']['discharge_date'],
                'payer' => $episode['administrative']['payer'],
                'class' => $episode['administrative']['class'],
            ],
        ]);
    }

    public function resume(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => [
                'keluhan_utama' => $episode['clinical']['chief_complaint'],
                'resume_text' => $episode['clinical']['resume_text'],
            ],
        ]);
    }

    public function diagnoses(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => collect($episode['clinical']['diagnoses'])
                ->map(fn ($item) => ['diagnosis' => $item])
                ->values(),
        ]);
    }

    public function procedures(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => collect($episode['clinical']['procedures'])
                ->map(fn ($item) => ['procedure' => $item])
                ->values(),
        ]);
    }

    public function billing(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => collect($episode['administrative']['billing_items'])
                ->map(fn ($item) => ['item_name' => $item])
                ->values(),
        ]);
    }

    public function sep(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => [
                'no_sep' => $episode['sep_no'],
            ],
        ]);
    }

    public function documents(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => $episode['documents'],
        ]);
    }

    public function labs(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => collect($episode['supporting']['labs'])
                ->map(fn ($item) => ['result' => $item])
                ->values(),
        ]);
    }

    public function radiology(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => collect($episode['supporting']['radiology'])
                ->map(fn ($item) => ['result' => $item])
                ->values(),
        ]);
    }

    public function operationReport(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => [
                'report_text' => $episode['clinical']['operation_report_text'],
            ],
        ]);
    }

    public function cppt(string $encounterId): JsonResponse
    {
        $episode = $this->findEncounter($encounterId);

        return response()->json([
            'success' => true,
            'data' => [
                'cppt_text' => $episode['clinical']['cppt_text'],
            ],
        ]);
    }

    protected function findEncounter(string $encounterId): array
    {
        $encounters = $this->mockEncounters();

        abort_unless(isset($encounters[$encounterId]), 404, 'Mock encounter tidak ditemukan.');

        return $encounters[$encounterId];
    }

    protected function mockEncounters(): array
    {
        return [
            'RJ-458899' => [
                'episode_no' => 'EP-RJ-458899',
                'encounter_id' => 'RJ-458899',
                'sep_no' => '0301R0010426V000123',
                'patient' => [
                    'mrn' => 'RM001122',
                    'name' => 'I Made Sudarma',
                    'gender' => 'L',
                    'dob' => '1978-01-10',
                ],
                'service' => [
                    'care_type' => 'rawat_inap',
                    'unit' => 'ICU',
                    'doctor' => 'dr. Ketut Arimbawa, Sp.PD',
                    'admission_date' => '2026-04-12 09:20:00',
                    'discharge_date' => '2026-04-15 14:35:00',
                ],
                'clinical' => [
                    'chief_complaint' => 'Sesak napas dan demam tinggi',
                    'resume_text' => 'Pasien dirawat dengan sepsis ec pneumonia berat. Selama perawatan pasien mendapat ventilator mekanik, antibiotik injeksi, monitoring intensif, dan terapi suportif. Kondisi membaik saat pulang.',
                    'diagnoses' => [
                        'Sepsis',
                        'Pneumonia berat',
                        'Hipertensi',
                    ],
                    'procedures' => [
                        'Ventilator mekanik',
                        'Pemasangan central line',
                    ],
                    'operation_report_text' => '',
                    'cppt_text' => 'Hari 1: pasien sesak berat, leukosit meningkat. Hari 2: terpasang ventilator. Hari 3: kondisi membaik, ventilator dilepas bertahap.',
                ],
                'supporting' => [
                    'labs' => [
                        'Leukosit 18000',
                        'CRP meningkat',
                        'Hb 12.4',
                    ],
                    'radiology' => [
                        'Thorax: infiltrat bilateral',
                    ],
                ],
                'administrative' => [
                    'payer' => 'BPJS',
                    'class' => 'Kelas 3',
                    'billing_items' => [
                        'ICU visit',
                        'Ventilator',
                        'Antibiotik injeksi',
                    ],
                ],
                'documents' => [
                    [
                        'type' => 'resume_medis',
                        'available' => true,
                        'file_url' => 'http://127.0.0.1:8000/storage/mock/resume_rj458899.pdf',
                        'file_name' => 'resume_rj458899.pdf',
                    ],
                    [
                        'type' => 'hasil_lab',
                        'available' => true,
                        'file_url' => 'http://127.0.0.1:8000/storage/mock/lab_rj458899.pdf',
                        'file_name' => 'lab_rj458899.pdf',
                    ],
                    [
                        'type' => 'laporan_operasi',
                        'available' => false,
                        'file_url' => null,
                        'file_name' => null,
                    ],
                ],
            ],

            'RI-458900' => [
                'episode_no' => 'EP-RI-458900',
                'encounter_id' => 'RI-458900',
                'sep_no' => '0301R0010426V000124',
                'patient' => [
                    'mrn' => 'RM001123',
                    'name' => 'Ni Luh Sari Dewi',
                    'gender' => 'P',
                    'dob' => '1986-09-25',
                ],
                'service' => [
                    'care_type' => 'rawat_inap',
                    'unit' => 'Rawat Inap Bedah',
                    'doctor' => 'dr. Komang Yasa, Sp.B',
                    'admission_date' => '2026-04-13 08:10:00',
                    'discharge_date' => '2026-04-15 11:20:00',
                ],
                'clinical' => [
                    'chief_complaint' => 'Nyeri perut kanan bawah',
                    'resume_text' => 'Pasien dirawat dengan appendicitis akut dan dilakukan appendektomi. Pasca operasi kondisi stabil dan diperbolehkan pulang.',
                    'diagnoses' => [
                        'Appendicitis akut',
                        'Anemia ringan',
                    ],
                    'procedures' => [
                        'Appendektomi',
                    ],
                    'operation_report_text' => 'Dilakukan appendektomi terbuka. Appendix tampak inflamed. Tidak ditemukan komplikasi intraoperatif.',
                    'cppt_text' => 'Hari 1: nyeri abdomen kanan bawah. Hari 2: operasi appendektomi. Hari 3: nyeri berkurang dan pasien stabil.',
                ],
                'supporting' => [
                    'labs' => [
                        'Leukosit 14500',
                        'Hb 10.9',
                    ],
                    'radiology' => [
                        'USG abdomen: appendicitis akut',
                    ],
                ],
                'administrative' => [
                    'payer' => 'BPJS',
                    'class' => 'Kelas 2',
                    'billing_items' => [
                        'Rawat inap bedah',
                        'Appendektomi',
                        'Obat pasca operasi',
                    ],
                ],
                'documents' => [
                    [
                        'type' => 'resume_medis',
                        'available' => true,
                        'file_url' => 'http://127.0.0.1:8000/storage/mock/resume_ri458900.pdf',
                        'file_name' => 'resume_ri458900.pdf',
                    ],
                    [
                        'type' => 'laporan_operasi',
                        'available' => true,
                        'file_url' => 'http://127.0.0.1:8000/storage/mock/op_ri458900.pdf',
                        'file_name' => 'op_ri458900.pdf',
                    ],
                    [
                        'type' => 'hasil_lab',
                        'available' => true,
                        'file_url' => 'http://127.0.0.1:8000/storage/mock/lab_ri458900.pdf',
                        'file_name' => 'lab_ri458900.pdf',
                    ],
                ],
            ],
        ];
    }
}