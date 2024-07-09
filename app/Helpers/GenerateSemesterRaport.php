<?php

namespace App\Helpers;

use App\Models\Grading;
use App\Models\Student;
use App\Models\ClassSchool;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAssessment;
use App\Models\HomeroomNotes;
use App\Models\LearningData;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MemberClassSchool;
use App\Models\MemberExtracurricular;
use App\Models\StudentAchievement;
use App\Models\StudentAttendance;

class GenerateSemesterRaport
{
    public static function makeData($id, $formattedDate)
    {
        $classSchool = ClassSchool::find($id);
        $sekolah = $classSchool->level->school;
        $title = 'Completeness of Report';
        $data_anggota_kelas = MemberClassSchool::where('class_school_id', $id)->where('academic_year_id', Helper::getActiveAcademicYearId())->get();
        $view = 'print.print-data-raport';
        $km_tgl_raport = $formattedDate;

        $pdf = Pdf::loadView($view, compact('title', 'sekolah', 'classSchool', 'data_anggota_kelas', 'km_tgl_raport'))->setPaper('A4', 'portrait'); // Use A4 paper size

        return $pdf->stream('report_data_' . $classSchool->name . '.pdf');
    }

    public static function make($id, $semester, $formattedDate)
    {
        $classSchool = ClassSchool::find($id);

        $memberClassSchool = MemberClassSchool::where('class_school_id', $id)->where('academic_year_id', Helper::getActiveAcademicYearId())->pluck('id');
        $data_anggota_kelas = MemberClassSchool::where('class_school_id', $id)->where('academic_year_id', Helper::getActiveAcademicYearId())->get();

        $sekolah = $classSchool->level->school;

        $learning_data = LearningData::where('class_school_id', $id)->get('id');

        $data_nilai_akhir_semester = [];

        for ($semester_id = 1; $semester_id <= 2; $semester_id++) {
            for ($term = 1; $term <= 2; $term++) {
                $data_nilai = Grading::where('term_id', $term)->where('semester_id', $semester_id)->whereIn('learning_data_id', $learning_data)->get();

                $nilai_akhir = [];
                foreach ($data_nilai as $nilai) {
                    $minimumCriteria = $nilai->learningData->minimumCriteria->where('class_school_id', $id)->first();

                    $nilai_akhir_raport = $nilai->nilai_revisi ? $nilai->nilai_revisi : $nilai->nilai_akhir;

                    $nilai_akhir[] = [
                        'anggota_kelas' => $nilai->member_class_school_id,
                        'learning_data_id' => $nilai->learning_data_id,
                        'nilai_akhir_raport' => $nilai_akhir_raport,
                        'nama_mapel' => $nilai->learningData->subject->name,
                        'nama_mapel_indonesian' => $nilai->learningData->subject->name_idn,
                        'kkm' => $minimumCriteria->kkm,
                        'deskripsi_nilai' => $nilai->description,
                        'semester_id' => $nilai->semester_id,
                    ];
                }

                foreach ($nilai_akhir as $nilai) {
                    $pembelajaran_id = $nilai['learning_data_id'];
                    $anggota_kelas_id = $nilai['anggota_kelas'];
                    $key = "nilai_akhir_term_{$term}_semester_{$semester_id}";

                    if (!isset($data_nilai_akhir_semester[$pembelajaran_id])) {
                        $data_nilai_akhir_semester[$pembelajaran_id] = [];
                    }

                    if (!isset($data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id])) {
                        $data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id] = [
                            'nilai' => 0, // Ensure this is initialized before using
                            'predikat' => '',
                            'nama_mapel' => '',
                            'nama_mapel_indonesian' => '',
                            'kkm' => '',
                            'deskripsi_nilai' => '',
                            'semester_id' => $semester_id,
                        ];
                    }

                    // Update values for the specific class member
                    $data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id][$key] = $nilai['nilai_akhir_raport'];
                    $data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id]['nilai'] += $nilai['nilai_akhir_raport'];
                    $data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id]['nama_mapel'] = $nilai['nama_mapel'];
                    $data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id]['nama_mapel_indonesian'] = $nilai['nama_mapel_indonesian'];
                    $data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id]['kkm'] = $nilai['kkm'];
                    $data_nilai_akhir_semester[$anggota_kelas_id][$pembelajaran_id]['deskripsi_nilai'] = $nilai['deskripsi_nilai'];
                }
            }

            // Hitung Final Grade untuk semester tertentu
            $data_nilai_akhir_semester = array_map(function ($members) {
                foreach ($members as $member_id => $data) {
                    $data['nilai'] /= 2;

                    $kkm = [
                        'predikat_a' => 80.0,
                        'predikat_b' => 70.0,
                        'predikat_c' => 60.0,
                        'predikat_d' => 0.0,
                    ];

                    if ($data['nilai'] >= $kkm['predikat_a'] && $data['nilai'] <= 100.0) {
                        $data['predikat'] = 'A';
                    } elseif ($data['nilai'] >= $kkm['predikat_b'] && $data['nilai'] < $kkm['predikat_a']) {
                        $data['predikat'] = 'B';
                    } elseif ($data['nilai'] >= $kkm['predikat_c'] && $data['nilai'] < $kkm['predikat_b']) {
                        $data['predikat'] = 'C';
                    } elseif ($data['nilai'] >= $kkm['predikat_d'] && $data['nilai'] <= $kkm['predikat_c']) {
                        $data['predikat'] = 'D';
                    } else {
                        $data['predikat'] = 'N/A';
                    }
                    $members[$member_id] = $data;
                }
                return $members;
            }, $data_nilai_akhir_semester);

            // Simpan hasil Final Grade semester dalam variabel sesuai semester
            if ($semester_id == 1) {
                $data_nilai_akhir_semester_1 = $data_nilai_akhir_semester;
            } elseif ($semester_id == 2) {
                $data_nilai_akhir_semester_2 = $data_nilai_akhir_semester;
            }

            if (isset($data_nilai_akhir_semester_1) && isset($data_nilai_akhir_semester_2)) {
                $data_nilai_akhir_total = [];

                // Iterate through each learning data ID
                foreach ($data_nilai_akhir_semester_1 as $member_id => $pembelajaran_data) {
                    foreach ($pembelajaran_data as $pembelajaran_id => $data_semester_1) {
                        // Check if the second semester has data for the same learning data ID and member ID
                        if (isset($data_nilai_akhir_semester_2[$member_id]) && isset($data_nilai_akhir_semester_2[$member_id][$pembelajaran_id])) {
                            $data_semester_2 = $data_nilai_akhir_semester_2[$member_id][$pembelajaran_id];

                            // Calculating the total final grade
                            $nilai_semester_1 = $data_semester_1['nilai'];
                            $nilai_semester_2 = $data_semester_2['nilai'];

                            $data_nilai_akhir_total[$member_id][$pembelajaran_id] = [
                                'nilai_akhir_total' => ($nilai_semester_1 + $nilai_semester_2) / 2,
                                'nama_mapel' => $data_semester_1['nama_mapel'],
                                'nama_mapel_indonesian' => $data_semester_1['nama_mapel_indonesian'],
                                'kkm' => $data_semester_1['kkm'],
                                'deskripsi_nilai' => $data_semester_1['deskripsi_nilai'],
                                'semester_id' => 2, // Assuming it means full academic year after both semesters are calculated
                                'predikat' => $data_semester_1['predikat'], // Assume it needs to be recalculated or chosen based on final grade
                                'nilai_akhir_term_1_semester_1' => $data_semester_1['nilai_akhir_term_1_semester_1'] ?? null,
                                'nilai_akhir_term_2_semester_1' => $data_semester_1['nilai_akhir_term_2_semester_1'] ?? null,
                                'nilai_akhir_term_1_semester_2' => $data_semester_2['nilai_akhir_term_1_semester_2'] ?? null,
                                'nilai_akhir_term_2_semester_2' => $data_semester_2['nilai_akhir_term_2_semester_2'] ?? null,
                                'nilai_akhir_semester_1' => $nilai_semester_1,
                                'nilai_akhir_semester_2' => $nilai_semester_2,
                            ];
                        }
                    }
                }
            } elseif (isset($data_nilai_akhir_semester_1)) {
                $data_nilai_akhir_total = [];

                // Isi data hanya dari semester 1
                foreach ($data_nilai_akhir_semester_1 as $member_id => $pembelajaran_data) {
                    foreach ($pembelajaran_data as $pembelajaran_id => $data_semester_1) {
                        if (!isset($data_nilai_akhir_total[$member_id])) {
                            $data_nilai_akhir_total[$member_id] = [];
                        }

                        $nilai_semester_1 = $data_semester_1['nilai'];

                        $data_nilai_akhir_total[$member_id][$pembelajaran_id] = [
                            'nilai_akhir_total' => $nilai_semester_1, // asumsi sementara karena belum ada semester 2
                            'nama_mapel' => $data_semester_1['nama_mapel'],
                            'nama_mapel_indonesian' => $data_semester_1['nama_mapel_indonesian'],
                            'kkm' => $data_semester_1['kkm'],
                            'deskripsi_nilai' => $data_semester_1['deskripsi_nilai'],
                            'semester_id' => 1, // Karena ini hanya semester 1
                            'predikat' => $data_semester_1['predikat'],
                            'nilai_akhir_term_1_semester_1' => $data_semester_1['nilai_akhir_term_1_semester_1'] ?? null,
                            'nilai_akhir_term_2_semester_1' => $data_semester_1['nilai_akhir_term_2_semester_1'] ?? null,
                            'nilai_akhir_term_1_semester_2' => null,
                            'nilai_akhir_term_2_semester_2' => null,
                            'nilai_akhir_semester_1' => $nilai_semester_1,
                            'nilai_akhir_semester_2' => null,
                        ];
                    }
                }
            }
        }

        $data_id_ekstrakulikuler = Extracurricular::where('academic_year_id', Helper::getActiveAcademicYearId())->get('id');
        $data_anggota_ekstrakulikuler = MemberExtracurricular::whereIn('extracurricular_id', $data_id_ekstrakulikuler)->get();
        foreach ($data_anggota_ekstrakulikuler as $anggota_ekstrakulikuler) {
            $cek_nilai_ekstra = ExtracurricularAssessment::where('member_extracurricular_id', $anggota_ekstrakulikuler->id)
                ->where('semester_id', $semester)
                ->first();
            if (is_null($cek_nilai_ekstra)) {
                $anggota_ekstrakulikuler->nilai = null;
                $anggota_ekstrakulikuler->deskripsi = null;
            } else {
                $anggota_ekstrakulikuler->nilai = $cek_nilai_ekstra->grade;
                $anggota_ekstrakulikuler->deskripsi = $cek_nilai_ekstra->description;
            }
        }

        $data_prestasi_siswa = StudentAchievement::join('member_class_schools', 'student_achievements.member_class_school_id', '=', 'member_class_schools.id')
            ->where('student_achievements.semester_id', $semester)
            ->where('member_class_schools.class_school_id', $id)
            ->select('student_achievements.*', 'member_class_schools.*') // Adjust the columns as needed
            ->get();

        $data_kehadiran_siswa = StudentAttendance::join('member_class_schools', 'student_attendances.member_class_school_id', '=', 'member_class_schools.id')
            ->where('student_attendances.semester_id', $semester)
            ->where('member_class_schools.class_school_id', $id)
            ->select('student_attendances.*', 'member_class_schools.*') // Adjust the columns as needed
            ->get();

        $data_catatan_wali_kelas = HomeroomNotes::join('member_class_schools', 'homeroom_notes.member_class_school_id', '=', 'member_class_schools.id')
            ->where('homeroom_notes.semester_id', $semester)
            ->where('member_class_schools.class_school_id', $id)
            ->select('homeroom_notes.*', 'member_class_schools.*') // Adjust the columns as needed
            ->get();
        $km_tgl_raport = $formattedDate;

        $title = 'Raport Semester - ' . $classSchool->name . ' - ' . $classSchool->level->name;

        $view = 'print.print-semester-raport';

        $pdf = Pdf::loadView($view, compact('title', 'sekolah', 'data_anggota_ekstrakulikuler', 'data_prestasi_siswa', 'data_kehadiran_siswa', 'data_catatan_wali_kelas', 'data_nilai', 'data_nilai_akhir_total', 'semester', 'classSchool', 'data_anggota_kelas', 'km_tgl_raport'))->setPaper('A4', 'portrait'); // Use A4 paper size

        return $pdf->stream('semester-' . $semester . '_report_' . $classSchool->name . '.pdf');
    }
}
