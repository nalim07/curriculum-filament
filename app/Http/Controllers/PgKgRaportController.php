<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\ClassSchool;
use Illuminate\Http\Request;
use App\Helpers\GeneratePgKgRaport;
use App\Helpers\GenerateMidSemesterRaport;

class PgKgRaportController extends Controller
{
    public function previewDataPgKgRaport(Request $request)
    {
        $livewire = json_decode($request->input('livewire'), true);
        $data = json_decode($request->input('data'), true);
        $classSchool = ClassSchool::find($data['class_school_id']);
        $date = $data['date'];
        $dateTime = new DateTime($date);
        $formattedDate = $dateTime->format('d F Y');

        return GeneratePgKgRaport::makeData($classSchool->id, $formattedDate);
    }

    public function previewPgKgRaport(Request $request)
    {
        $livewire = json_decode($request->input('livewire'), true);
        $data = json_decode($request->input('data'), true);
        $classSchool = ClassSchool::find($data['class_school_id']);
        $date = $data['date'];
        $dateTime = new DateTime($date);
        $formattedDate = $dateTime->format('d F Y');

        $term = $data['term_id'];

        return GeneratePgKgRaport::make($classSchool->id, $term, $formattedDate);
    }
}
