<?php

namespace App\Filament\Widgets;

use App\Models\Level;
use App\Models\School;
use App\Helpers\Helper;
use Filament\Widgets\Widget;

class AcademicYearWidget extends Widget
{
    protected static string $view = 'filament.widgets.academic-year';

    protected function getViewData(): array
    {
        $currentAcademicYear  = Helper::getActiveAcademicYearName();
        $termPG = Level::where('school_id', 1)->pluck('term_id')->first();
        $termPS = Level::where('school_id', 2)->pluck('term_id')->first();
        $termJHS = Level::where('school_id', 3)->pluck('term_id')->first();
        $termSHS = Level::where('school_id', 4)->pluck('term_id')->first();

        $semesterPS = Level::where('school_id', 2)->pluck('semester_id')->first();
        $semesterJHS = Level::where('school_id', 3)->pluck('semester_id')->first();
        $semesterSHS = Level::where('school_id', 4)->pluck('semester_id')->first();

        return [
            'currentAcademicYear' => $currentAcademicYear,
            'termPG' => $termPG,
            'termPS' => $termPS,
            'termJHS' => $termJHS,
            'termSHS' => $termSHS,

            'semesterPS' => $semesterPS,
            'semesterJHS' => $semesterJHS,
            'semesterSHS' => $semesterSHS
        ];
    }
}
