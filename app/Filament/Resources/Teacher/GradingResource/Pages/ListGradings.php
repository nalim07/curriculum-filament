<?php

namespace App\Filament\Resources\Teacher\GradingResource\Pages;

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
use App\Models\LearningData;
use App\Models\PlanSumatifValue;
use Filament\Notifications\Notification;
use App\Models\PlanFormatifValue;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use App\Models\MemberClassSchool;
use App\Filament\Resources\Teacher\GradingResource;

class ListGradings extends ListRecords
{
    protected static string $resource = GradingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Select Student')
                ->form([
                    Hidden::make('plan_sumatif_value_id'),
                    Select::make('plan_formatif_value_id')
                        ->relationship('planFormatifValue.learningData', 'id', function ($query) {
                            if (auth()->user()->hasRole('super_admin')) {
                                return $query->with('subject')->whereHas('classSchool', function (Builder $query) {
                                    $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                });
                            } else {
                                $user = auth()->user();
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $teacherId = $user->employee->teacher->id;
                                    return $query->with('subject')
                                        ->whereHas('classSchool', function (Builder $query) {
                                            $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                        })->where('teacher_id', $teacherId);
                                }
                                return $query->with('subject')->whereHas('classSchool', function (Builder $query) {
                                    $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                });
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject->name . ' - ' . $record->classSchool->name)
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Learning Data')
                        ->live()
                        ->helperText('If empty learning data, please fill the marking schema first.')
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            $activeAcademicYearId = Helper::getActiveAcademicYearId();
                            $planFormatifValue = PlanFormatifValue::find($state);
                            if ($planFormatifValue) {
                                $user = auth()->user();
                                if ($user->hasRole('super_admin')) {
                                    $learningData = LearningData::with('classSchool.level.semester')->find($planFormatifValue->learning_data_id);
                                } else {
                                    if ($user && $user->employee && $user->employee->teacher) {
                                        $learningData = LearningData::with('classSchool.level.semester')
                                            ->where('teacher_id', $user->employee->teacher->id)
                                            ->find($planFormatifValue->learning_data_id);
                                    }
                                }
                                $semesterId = $learningData ? $learningData->classSchool->level->semester->id : null;
                                $termId = $learningData ? $learningData->classSchool->level->term->id : null;
                                $set('semester_id', $semesterId);
                                $set('term_id', $termId);

                                $planSumatifValue = PlanSumatifValue::where('learning_data_id', $learningData->id)
                                    ->where('semester_id', $semesterId)
                                    ->where('term_id', $termId)
                                    ->first();

                                if ($planSumatifValue) {
                                    $set('plan_sumatif_value_id', $planSumatifValue->id);
                                }

                                $planFormatifValue = PlanFormatifValue::where('learning_data_id', $learningData->id)
                                    ->where('semester_id', $semesterId)
                                    ->where('term_id', $termId)
                                    ->first();

                                if ($planFormatifValue) {
                                    $set('plan_formatif_value_id', $planFormatifValue->id);
                                }
                            }
                        })->columnSpanFull(),

                    Select::make('semester_id')
                        ->label('Semester')
                        ->default(function (Get $get) {
                            $planFormatifValue = PlanFormatifValue::find($get('plan_formatif_value_id'));
                            if ($planFormatifValue) {
                                $semester = $planFormatifValue->learningData->classSchool->level->semester_id;
                                return $semester ? $semester : null;
                            }
                            return null;
                        })
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options([
                            '1' => '1',
                            '2' => '2'
                        ]),

                    Select::make('term_id')
                        ->label('Term')
                        ->default(function (Get $get) {
                            $planFormatifValue = PlanFormatifValue::find($get('plan_formatif_value_id'));
                            if ($planFormatifValue) {
                                $term = $planFormatifValue->learningData->classSchool->level->term_id;
                                return $term ? $term : null;
                            }
                            return null;
                        })
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options([
                            '1' => '1',
                            '2' => '2'
                        ]),

                    CheckboxList::make('member_class_school_id')
                        ->label('Students')
                        ->rules(function ($get) {
                            // Assuming 'plan_formatif_value_id' is available in the context
                            $planFormatifValueId = $get('plan_formatif_value_id');
                            $semesterId = $get('semester_id');
                            $termId = $get('term_id');

                            return [
                                Rule::unique('gradings', 'member_class_school_id')
                                    ->where(function ($query) use ($planFormatifValueId, $semesterId, $termId) {
                                        return $query
                                            ->where('plan_formatif_value_id', $planFormatifValueId)
                                            ->where('semester_id', $semesterId)
                                            ->where('term_id', $termId);
                                    }),
                            ];
                        })
                        ->options(function (Get $get) {
                            $selectedPlanFormatifValue = PlanFormatifValue::find($get('plan_formatif_value_id'));
                            if ($selectedPlanFormatifValue) {
                                $learningData = LearningData::with(['classSchool', 'subject'])
                                    ->where('id', $selectedPlanFormatifValue->learning_data_id)
                                    ->first();

                                if ($learningData) {
                                    $classSchool = ClassSchool::query()
                                        ->where('id', $learningData->class_school_id)
                                        ->first();

                                    $slugSubject = $learningData->subject->slug;

                                    $subjectFirstWord = explode('-', $slugSubject)[0];

                                    $religion = null;

                                    if (strtolower($subjectFirstWord) === 'religion') {
                                        $slug = explode('-', $slugSubject);
                                        if (count($slug) > 1) {
                                            $slugReligion = strtolower($slug[1]);

                                            switch ($slugReligion) {
                                                case 'islam':
                                                    $religion = 1;
                                                    break;
                                                case 'protestan':
                                                    $religion = 2;
                                                    break;
                                                case 'katolik':
                                                    $religion = 3;
                                                    break;
                                                case 'hindu':
                                                    $religion = 4;
                                                    break;
                                                case 'budha':
                                                    $religion = 5;
                                                    break;
                                                case 'khonghucu':
                                                    $religion = 6;
                                                    break;
                                                case 'lainnya':
                                                    $religion = 7;
                                                    break;
                                                default:
                                                    $religion = null;
                                                    break;
                                            }
                                        }
                                    }

                                    $memberClassSchool = MemberClassSchool::query()
                                        ->where('class_school_id', $classSchool->id)
                                        ->where('academic_year_id', $classSchool->academic_year_id)
                                        ->whereHas('student', function ($query) use ($religion) {
                                            if ($religion !== null) {
                                                $query->where('religion', $religion);
                                            }
                                        })
                                        ->get()
                                        ->pluck('id')
                                        ->toArray();

                                    return Student::whereIn('id', $memberClassSchool)->get()->pluck('fullname', 'id');
                                }
                            }
                        })
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(2),
                ])
                ->action(function (array $data): void {
                    $dataArray = [];
                    $getMemberClassSchoolId = $data['member_class_school_id'];

                    if (!count($getMemberClassSchoolId)) {
                        Notification::make()->warning()->title('Whopps, cant do that :(')->body('No student selected')->send();
                    } elseif ($data['plan_sumatif_value_id'] == null) {
                        Notification::make()->warning()->title('Whopps, cant do that :(')->body('please create plan sumatif for selected term and semester')->send();
                    } elseif ($data['plan_formatif_value_id'] == null) {
                        Notification::make()->warning()->title('Whopps, cant do that :(')->body('please create plan formatif for selected term and semester')->send();
                    } else {
                        for ($i = 0; $i < count($getMemberClassSchoolId); $i++) {

                            //Get learning data id from plan_formatif_value_id
                            $planFormatifValue = PlanFormatifValue::find($data['plan_formatif_value_id']);
                            $learningDataId = $planFormatifValue->learning_data_id;

                            $dataArray = [
                                'semester_id' => $data['semester_id'],
                                'term_id' => $data['term_id'],
                                'learning_data_id' => $learningDataId,
                                'member_class_school_id' => $getMemberClassSchoolId[$i],
                                'plan_formatif_value_id' => $data['plan_formatif_value_id'],
                                'plan_sumatif_value_id' => $data['plan_sumatif_value_id'],
                            ];

                            // Check if the member_class_school_id exists in member_class_schools table
                            $exists = DB::table('member_class_schools')
                                ->where('id', $getMemberClassSchoolId[$i])
                                ->exists();

                            if (!$exists) {
                                Notification::make()
                                    ->warning()
                                    ->title('Warning')
                                    ->body('Member class school ID ' . $getMemberClassSchoolId[$i] . ' does not exist.')
                                    ->send();

                                Log::warning('Member class school ID does not exist', [
                                    'member_class_school_id' => $getMemberClassSchoolId[$i],
                                ]);

                                continue; // Skip to the next iteration
                            }

                            try {
                                // Insert data
                                DB::table('gradings')->insert($dataArray);

                                // Send success notification
                                Notification::make()->success()->title('Yeayy, success!')->body('Successfully added data')->send();
                            } catch (\Exception $e) {
                                // Send error notification
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body('An error occurred: ' . $e->getMessage())
                                    ->send();

                                // Log the error for debugging
                                Log::error('Error inserting gradings: ' . $e->getMessage(), [
                                    'dataArray' => $dataArray,
                                ]);
                            }
                        }
                    }
                }),
        ];
    }
}
