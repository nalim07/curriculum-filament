<?php

namespace App\Filament\Resources\Teacher\ExtracurricularAssessmentResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Actions\Action;
use Illuminate\Validation\Rule;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\ClassSchool;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Extracurricular;
use Filament\Forms\Components\CheckboxList;
use App\Models\MemberClassSchool;
use App\Models\MemberExtracurricular;
use App\Filament\Resources\Teacher\ExtracurricularAssessmentResource;

class ListExtracurricularAssessments extends ListRecords
{
    protected static string $resource = ExtracurricularAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Select Student')
                ->form([
                    Select::make('extracurricular_id')
                        ->relationship('planFormatifValue.learningData', 'id', function ($query) {
                            if (auth()->user()->hasRole('super_admin')) {
                                return $query->with('subject')->whereHas('classSchool', function (Builder $query) {
                                    $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                });
                            } else {
                                $user = auth()->user();
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $teacherId = $user->employee->teacher->id;
                                    return $query
                                        ->with('subject')
                                        ->whereHas('classSchool', function (Builder $query) {
                                            $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                        })
                                        ->where('teacher_id', $teacherId);
                                }
                                return $query->with('subject')->whereHas('classSchool', function (Builder $query) {
                                    $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                });
                            }
                        })
                        ->relationship('extracurricular', 'id', function ($query) {
                            if (auth()->user()->hasRole('super_admin')) {
                                return $query->with('teacher');
                            } else {
                                $user = auth()->user();
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $teacherId = $user->employee->teacher->id;
                                    return $query->with('subject')->where('teacher_id', $teacherId);
                                }
                                return $query->with('teacher');
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Extracurricular')
                        ->live(),
                    CheckboxList::make('member_extracurricular_id')
                        ->label('Students')
                        ->rules(function ($get) {
                            // Assuming 'plan_formatif_value_id' is available in the context
                            $extracurricularId = $get('extracurricular_id');

                            return [
                                Rule::unique('extracurricular_assessments', 'member_extracurricular_id')->where(function ($query) use ($extracurricularId) {
                                    return $query->where('extracurricular_id', $extracurricularId);
                                }),
                            ];
                        })
                        ->options(function (Get $get) {
                            $selectedExtracurricular = Extracurricular::find($get('extracurricular_id'));

                            if ($selectedExtracurricular) {
                                if ($selectedExtracurricular->memberExtracurricular) {
                                    $memberExtracurricular = $selectedExtracurricular->memberExtracurricular->pluck('member_class_school_id')
                                        ->toArray();

                                    return MemberClassSchool::whereIn('id', $memberExtracurricular)->get()->pluck('student.fullname', 'id');
                                }
                            }

                            return collect(); // Return an empty collection if the checks fail
                        })
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(3),
                ])
                ->action(function (array $data): void {
                    $dataArray = [];
                    $getMemberExtracurricularId = $data['member_extracurricular_id'];

                    if (!count($getMemberExtracurricularId)) {
                        Notification::make()->warning()->title('Whopps, cant do that :(')->body('No student selected')->send();
                    } else {
                        for ($i = 0; $i < count($getMemberExtracurricularId); $i++) {
                            $dataArray = [
                                'extracurricular_id' => $data['extracurricular_id'],
                                'member_extracurricular_id' => $getMemberExtracurricularId[$i],
                            ];

                            $exists = DB::table('member_extracurriculars')
                                ->where('id', $getMemberExtracurricularId[$i])
                                ->exists();

                            if (!$exists) {
                                Notification::make()
                                    ->warning()
                                    ->title('Warning')
                                    ->body('Member extracurricular ID ' . $getMemberExtracurricularId[$i] . ' does not exist.')
                                    ->send();

                                Log::warning('Member extracurricular ID does not exist', [
                                    'member_class_school_id' => $getMemberExtracurricularId[$i],
                                ]);

                                continue;
                            }

                            try {
                                DB::table('extracurricular_assessments')->insert($dataArray);

                                // Send success notification
                                Notification::make()->success()->title('Yeayy, success!')->body('Successfully added data')->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body('An error occurred: ' . $e->getMessage())
                                    ->send();

                                // Log the error for debugging
                                Log::error('Error inserting extracurricular_assessments: ' . $e->getMessage(), [
                                    'dataArray' => $dataArray,
                                ]);
                            }
                        }
                    }
                }),
        ];
    }
}
