<?php

namespace App\Filament\Resources\MasterData\ClassResource\Widgets;

use App\Models\ClassSchool;
use App\Models\LearningData;
use App\Models\Extracurricular;
use App\Models\MemberClassSchool;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class NumberOfClassesTaught extends BaseWidget
{
    protected function getCards(): array
    {
        $user = auth()->user();
        $teacher = $user->employee ? $user->employee->teacher : null;

        if ($teacher) {
            $teacherId = $teacher->id;

            $classCount = ClassSchool::where('teacher_id', $teacherId)->count();

            $uniqueSubjectCount = LearningData::where('teacher_id', $teacherId)
                ->distinct('subject_id')
                ->count('subject_id');

            $classSchoolIds = ClassSchool::where('teacher_id', $teacherId)->pluck('id');

            $studentCount = MemberClassSchool::whereIn('class_school_id', $classSchoolIds)->count();

            $extracurricularCount = Extracurricular::where('teacher_id', $teacherId)->count();
        } else {
            // Jika teacher adalah null
            $classCount = 0;
            $uniqueSubjectCount = 0;
            $classSchoolIds = [];
            $studentCount = 0;
            $extracurricularCount = 0;
        }

        return [
            Card::make('Number of Classes', $classCount)
                ->icon('heroicon-o-view-columns'),
            Card::make('Number of Subjects', $uniqueSubjectCount)
                ->icon('heroicon-o-book-open'),
            Card::make('Number of Students', $studentCount)
                ->icon('heroicon-o-user-group'),
            Card::make('Number of Extracurriculars', $extracurricularCount)
                ->icon('heroicon-o-rocket-launch'),
        ];
        return [
            Card::make('Number of Classes', ClassSchool::where('teacher_id', $teacherId)->count())
                ->icon('heroicon-o-user-group'),
            Card::make('Number of Subjects', $uniqueSubjectCount)
                ->icon('heroicon-o-currency-dollar'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
