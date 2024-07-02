<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Student;
use App\Models\ClassSchool;
use Illuminate\Http\Request;
use App\Helpers\GenerateSemesterRaport;

class KMSemesterRaportController extends Controller
{
    public function previewDataRaport(Request $request)
    {
        $livewire = json_decode($request->input('livewire'), true);
        $data = json_decode($request->input('data'), true);
        $classSchool = ClassSchool::find($data['class_school_id']);
        $date = $data['date'];
        $dateTime = new DateTime($date);
        $formattedDate = $dateTime->format('d F Y');

        return GenerateSemesterRaport::makeData($classSchool->id, $formattedDate);
    }

    public function previewSemesterRaport(Request $request)
    {
        $livewire = json_decode($request->input('livewire'), true);
        $data = json_decode($request->input('data'), true);
        $classSchool = ClassSchool::find($data['class_school_id']);
        $date = $data['date'];
        $dateTime = new DateTime($date);
        $formattedDate = $dateTime->format('d F Y');

        $semester = $request->input('semester_id');

        return GenerateSemesterRaport::make($classSchool->id, $semester, $formattedDate);
    }
}
