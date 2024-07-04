<?php

namespace App\Helpers;

use App\Helpers\Helper;
use App\Models\Grading;
use App\Models\Student;
use App\Models\TkEvent;
use App\Models\TkPoint;
use App\Models\TkTopic;
use App\Models\TkElement;
use App\Models\TkSubtopic;
use App\Models\ClassSchool;
use App\Models\LearningData;
use App\Models\TkAttendance;
use App\Models\HomeroomNotes;
use App\Models\Extracurricular;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MemberClassSchool;
use App\Models\StudentAttendance;
use App\Models\TkAchivementGrade;
use App\Models\StudentAchievement;
use App\Models\MemberExtracurricular;
use App\Models\TkAchivementEventGrade;
use App\Models\ExtracurricularAssessment;

class GeneratePgKgRaport
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


    public static function make($id, $term, $formattedDate)
    {
        $classSchool = ClassSchool::find($id);
        $academicYear = $classSchool->academicYear;
        $term_id = $term;

        $memberClassSchool = MemberClassSchool::where('class_school_id', $id)->where('academic_year_id', Helper::getActiveAcademicYearId())->pluck('id');
        $data_anggota_kelas = MemberClassSchool::where('class_school_id', $id)->where('academic_year_id', Helper::getActiveAcademicYearId())->get();

        $sekolah = $classSchool->level->school;

        $dataTkElements = TkElement::all();
        $dataTkTopics = TkTopic::all();
        $dataTkSubtopics = TkSubtopic::all();
        $dataTkPoints = TkPoint::where('term_id', $term_id)->get();

        // Achivements
        $dataAchivements = TkAchivementGrade::get(['member_class_school_id', 'tk_point_id', 'achivement']);
        $dataAchivementEvents = TkAchivementEventGrade::get(['member_class_school_id', 'tk_event_id', 'achivement_event']);
        $dataAttendance = TkAttendance::get(['member_class_school_id', 'no_school_days', 'days_attended', 'days_absent']);
        $dataCatatanWalikelas = HomeroomNotes::get(['member_class_school_id', 'notes']);

        // EVENTS
        $dataEvents = TkEvent::where('academic_year_id', Helper::getActiveAcademicYearId())->where('term_id', $term_id)->get();

        $km_tgl_raport = $formattedDate;

        $title = 'Raport Mid-Semester - ' . $classSchool->name . ' - ' . $classSchool->level->name;

        $view = 'print.print-pg-kg-raport';

        $pdf = Pdf::loadView(
            $view,
            compact('title', 'sekolah', 'data_anggota_kelas',  'term', 'dataTkElements', 'dataTkTopics', 'dataTkSubtopics', 'dataTkPoints', 'dataAchivements', 'dataEvents', 'dataAchivementEvents', 'dataAttendance', 'classSchool', 'dataCatatanWalikelas', 'km_tgl_raport')
        )->setPaper('A4', 'portrait'); // Use A4 paper size

        // $pdf->render();
        // $dompdf = $pdf->getDomPDF();
        // $font = $dompdf->getFontMetrics()->get_font("Arial", "bold");
        // $margin = 10; // margin in points
        // $width = 193.8 * 2.83; // width of the page in points
        // $height = 290 * 2.83; // height of the page in points

        // $x = $width - $margin;
        // $y = $height - $margin;
        // $left = 22.5;

        // $font_size = 6;
        // $font_color_black = array(0, 0, 0); // RGB for black color

        // $dompdf->get_canvas()->page_text($left, $y, $classSchool->name . $academicYear->year, $font, $font_size, $font_color_black, 0, 0, 0, 'L');

        // $dompdf->get_canvas()->page_text($x, $y, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, $font_size, $font_color_black, 0, 0, 0, 'R');

        return $pdf->stream('term-' . $term . '_report_' . $classSchool->name . '.pdf');
    }
}
