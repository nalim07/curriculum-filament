<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use App\Models\Grading;
use App\Models\Student;
use App\Models\Subject;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassSchool;
use App\Models\LearningData;
use Filament\Actions\Action;
use App\Models\PlanSumatifValue;
use Filament\Resources\Resource;
use App\Models\MemberClassSchool;
use App\Models\PlanFormatifValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\GradingResource\Pages;
use App\Filament\Resources\Teacher\GradingResource\RelationManagers;

class GradingResource extends Resource
{
    protected static ?string $model = Grading::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'grading';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    protected static function calculateAverage($data, $record)
    {
        $formatif_techniques = ['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5'];

        $sumatif_techniques = ['sumatif_technique_1', 'sumatif_technique_2'];

        $formatif_values = array_map(function ($technique) use ($data) {
            return (float) ($data[$technique] ?? 0);
        }, $formatif_techniques);

        $sumatif_values = array_map(function ($technique) use ($data) {
            return (float) ($data[$technique] ?? 0);
        }, $sumatif_techniques);

        $formatif_weights = array_map(function ($index) use ($record) {
            return (float) ($record->planFormatifValue->techniques[$index]->weighting ?? 1);
        }, array_keys($formatif_techniques));

        $sumatif_weights = array_map(function ($index) use ($record) {
            return (float) ($record->planSumatifValue->techniques[$index]->weighting ?? 1);
        }, array_keys($sumatif_techniques));

        $formatif_weighted_sum = array_sum(
            array_map(
                function ($value, $weight) {
                    return $value * $weight;
                },
                $formatif_values,
                $formatif_weights,
            ),
        );

        $sumatif_weighted_sum = array_sum(
            array_map(
                function ($value, $weight) {
                    return $value * $weight;
                },
                $sumatif_values,
                $sumatif_weights,
            ),
        );

        $total_weights = array_sum($formatif_weights) + array_sum($sumatif_weights);

        return ($formatif_weighted_sum + $sumatif_weighted_sum) / $total_weights;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('planFormatifValue.learningData.subject.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('memberClassSchool.student.fullname')
                    ->searchable()
                    ->sortable(),
                ColumnGroup::make('Formatif Value', [
                    TextInputColumn::make('formatif_technique_1')
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->alignment(Alignment::Center)
                        ->label('F1')
                        ->afterStateUpdated(function ($record, $state) {
                            $data = $record->only(['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5', 'sumatif_technique_1', 'sumatif_technique_2']);
                            $average = self::calculateAverage($data, $record);
                            $record->nilai_akhir = $average;
                            $record->save();
                        })
                        ->tooltip(fn ($record): string => self::getTechniqueFormatif($record, 0, $record->planFormatifValue->semester_id, $record->planFormatifValue->term_id)),
                    TextInputColumn::make('formatif_technique_2')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->label('F2')
                        ->afterStateUpdated(function ($record, $state) {
                            $data = $record->only(['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5', 'sumatif_technique_1', 'sumatif_technique_2']);
                            $average = self::calculateAverage($data, $record);
                            $record->nilai_akhir = $average;
                            $record->save();
                        })
                        ->tooltip(fn ($record): string => self::getTechniqueFormatif($record, 1, $record->planFormatifValue->semester_id, $record->planFormatifValue->term_id)),
                    TextInputColumn::make('formatif_technique_3')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->label('F3')
                        ->afterStateUpdated(function ($record, $state) {
                            $data = $record->only(['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5', 'sumatif_technique_1', 'sumatif_technique_2']);
                            $average = self::calculateAverage($data, $record);
                            $record->nilai_akhir = $average;
                            $record->save();
                        })
                        ->tooltip(fn ($record): string => self::getTechniqueFormatif($record, 2, $record->planFormatifValue->semester_id, $record->planFormatifValue->term_id)),
                    TextInputColumn::make('formatif_technique_4')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->label('F4')
                        ->afterStateUpdated(function ($record, $state) {
                            $data = $record->only(['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5', 'sumatif_technique_1', 'sumatif_technique_2']);
                            $average = self::calculateAverage($data, $record);
                            $record->nilai_akhir = $average;
                            $record->save();
                        })
                        ->tooltip(fn ($record): string => self::getTechniqueFormatif($record, 3, $record->planFormatifValue->semester_id, $record->planFormatifValue->term_id)),
                    TextInputColumn::make('formatif_technique_5')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->label('F5')
                        ->afterStateUpdated(function ($record, $state) {
                            $data = $record->only(['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5', 'sumatif_technique_1', 'sumatif_technique_2']);
                            $average = self::calculateAverage($data, $record);
                            $record->nilai_akhir = $average;
                            $record->save();
                        })
                        ->tooltip(fn ($record): string => self::getTechniqueFormatif($record, 4, $record->planFormatifValue->semester_id, $record->planFormatifValue->term_id)),
                ])->alignment(Alignment::Center),
                ColumnGroup::make('Formatif Value', [
                    TextInputColumn::make('sumatif_technique_1')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->label('S1')
                        ->afterStateUpdated(function ($record, $state) {
                            $data = $record->only(['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5', 'sumatif_technique_1', 'sumatif_technique_2']);
                            $average = self::calculateAverage($data, $record);
                            $record->nilai_akhir = $average;
                            $record->save();
                        })
                        ->tooltip(fn ($record): string => self::getTechniqueSumatif($record, 0, $record->planSumatifValue->semester_id, $record->planSumatifValue->term_id)),
                    TextInputColumn::make('sumatif_technique_2')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->label('S2')
                        ->afterStateUpdated(function ($record, $state) {
                            $data = $record->only(['formatif_technique_1', 'formatif_technique_2', 'formatif_technique_3', 'formatif_technique_4', 'formatif_technique_5', 'sumatif_technique_1', 'sumatif_technique_2']);
                            $average = self::calculateAverage($data, $record);
                            $record->nilai_akhir = $average;
                            $record->save();
                        })
                        ->tooltip(fn ($record): string => self::getTechniqueSumatif($record, 1, $record->planSumatifValue->semester_id, $record->planSumatifValue->term_id)),
                ])->alignment(Alignment::Center),
                ColumnGroup::make('Report Value', [
                    TextInputColumn::make('nilai_akhir')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->disabled()
                        ->label('Final Grade'),
                    TextInputColumn::make('nilai_revisi')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric', 'min:0', 'max:100'])
                        ->label('Revision Grade'),
                ])->alignment(Alignment::Center),
            ])
            ->filters(
                [
                    Tables\Filters\SelectFilter::make('learning_data_id')
                        ->label('Learning Data')
                        ->relationship('planFormatifValue.learningData', 'id', function ($query) {
                            if (auth()->user()->hasRole('super_admin')) {
                                return $query->with(['subject', 'classSchool'])->whereHas('classSchool', function (Builder $query) {
                                    $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                });
                            } else {
                                $user = auth()->user();
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $teacherId = $user->employee->teacher->id;
                                    return $query->with(['subject', 'classSchool'])
                                        ->whereHas('classSchool', function (Builder $query) {
                                            $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                        })->where('teacher_id', $teacherId);
                                }
                                return $query->with(['subject', 'classSchool']);
                            }
                        })
                        ->getOptionLabelFromRecordUsing(function ($record) {
                            return $record->subject->name . ' - ' . $record->classSchool->name;
                        })
                        ->searchable()
                        ->preload()
                        ->default(function () {
                            $query = auth()->user()->hasRole('super_admin') ?
                                PlanFormatifValue::with(['learningData' => function ($query) {
                                    $query->with(['subject', 'classSchool'])->first();
                                }])->first() :
                                PlanFormatifValue::whereHas('learningData', function (Builder $query) {
                                    $query->with(['subject', 'classSchool'])
                                        ->whereHas('classSchool', function (Builder $query) {
                                            $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                        })
                                        ->where('teacher_id', auth()->user()->employee->teacher->id);
                                })->first();

                            return $query ? $query->learningData->id : null;
                        }),

                    Tables\Filters\SelectFilter::make('semester_id')
                        ->label('Semester')
                        ->default(function (Get $get) {
                            $user = Auth::user();
                            $learningDataId = null;
                            if ($user->hasRole('super_admin')) {
                                $planFormatifValue = PlanFormatifValue::with('learningData.classSchool.level')->first();
                                if ($planFormatifValue) {
                                    $learningDataId = $planFormatifValue->learning_data_id;
                                }
                            } else {
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $planFormatifValue = PlanFormatifValue::whereHas('learningData', function (Builder $query) use ($user) {
                                        $query->with('classSchool.level')
                                            ->where('teacher_id', $user->employee->teacher->id)
                                            ->whereHas('classSchool', function (Builder $query) {
                                                $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                            });
                                    })->first();
                                    if ($planFormatifValue) {
                                        $learningDataId = $planFormatifValue->learning_data_id;
                                    }
                                }
                            }

                            if ($learningDataId) {
                                $learningData = LearningData::with('classSchool.level')->find($learningDataId);
                                if ($learningData && $learningData->classSchool && $learningData->classSchool->level) {
                                    return $learningData->classSchool->level->semester_id ?? null;
                                }
                            }

                            return null;
                        })
                        ->relationship('semester', 'semester')
                        ->searchable()
                        ->preload(),

                    Tables\Filters\SelectFilter::make('term_id')
                        ->label('Term')
                        ->default(function (Get $get) {
                            $user = Auth::user();
                            $learningDataId = null;
                            if ($user->hasRole('super_admin')) {
                                $planFormatifValue = PlanFormatifValue::with('learningData.classSchool.level')->first();
                                if ($planFormatifValue) {
                                    $learningDataId = $planFormatifValue->learning_data_id;
                                }
                            } else {
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $planFormatifValue = PlanFormatifValue::whereHas('learningData', function (Builder $query) use ($user) {
                                        $query->with('classSchool.level')
                                            ->where('teacher_id', $user->employee->teacher->id)
                                            ->whereHas('classSchool', function (Builder $query) {
                                                $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                            });
                                    })->first();
                                    if ($planFormatifValue) {
                                        $learningDataId = $planFormatifValue->learning_data_id;
                                    }
                                }
                            }

                            if ($learningDataId) {
                                $learningData = LearningData::with('classSchool.level')->find($learningDataId);
                                if ($learningData && $learningData->classSchool && $learningData->classSchool->level) {
                                    return $learningData->classSchool->level->term_id ?? null;
                                }
                            }

                            return null;
                        })
                        ->options([
                            '1' => '1',
                            '2' => '2',
                        ])
                        ->searchable()
                        ->preload()
                ],
                layout: FiltersLayout::AboveContent,
            )
            ->deselectAllRecordsWhenFiltered(false)
            ->filtersFormColumns(3)
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getTechniqueFormatif($record, $index, $semesterId, $termId)
    {
        // First, check if the record's semester and term match the provided ids
        if ($record->semester_id !== $semesterId || $record->term_id !== $termId) {
            return 'Record does not match the specified semester and term.';
        }

        // Proceed if the semester and term match
        $techniques = $record->planFormatifValue->techniques ?? [];

        // Check if the specific index exists in the techniques array
        if (isset($techniques[$index])) {
            $techniqueCode = $techniques[$index]->code;
            $techniqueName = Helper::getPlanFormatifTechnique($techniques[$index]->technique);
            $techniqueKKM = $techniques[$index]->weighting;
            return 'Code: ' . e($techniqueCode) . ', Technique: ' . e($techniqueName) . ', KKM: ' . e($techniqueKKM);
        }

        return 'No technique available';
    }

    protected static function getTechniqueSumatif($record, $index, $semesterId, $termId)
    {
        // First, check if the record's semester and term match the provided ids
        if ($record->semester_id !== $semesterId || $record->term_id !== $termId) {
            return 'Record does not match the specified semester and term.';
        }

        // Proceed if the semester and term match
        $techniques = $record->planSumatifValue->techniques ?? [];

        // Check if the specific index exists in the techniques array
        if (isset($techniques[$index])) {
            $techniqueCode = $techniques[$index]->code;
            $techniqueName = Helper::getPlanSumatifTechnique($techniques[$index]->technique);
            $techniqueKKM = $techniques[$index]->weighting;
            return 'Code: ' . e($techniqueCode) . ', Technique: ' . e($techniqueName) . ', KKM: ' . e($techniqueKKM);
        }

        return 'No technique available';
    }


    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('super_admin')) {
            return parent::getEloquentQuery()->whereHas('memberClassSchool.classSchool.academicYear', function (Builder $query) {
                $query->where('id', Helper::getActiveAcademicYearId());
            });
        } else {
            return parent::getEloquentQuery()->whereHas('memberClassSchool.classSchool.academicYear', function (Builder $query) {
                $query->where('id', Helper::getActiveAcademicYearId());
            })->whereHas('planFormatifValue.learningData.teacher', function (Builder $query) {
                $user = auth()->user();
                if ($user && $user->employee && $user->employee->teacher) {
                    $teacherId = $user->employee->teacher->id;
                    $query->where('teacher_id', $teacherId);
                }
            });
        }
    }

    public static function getRecord($key): Model
    {
        return static::getEloquentQuery()->findOrFail($key);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.nav_group.report_km');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradings::route('/'),
            // 'create' => Pages\CreateGrading::route('/create'),
            // 'edit' => Pages\EditGrading::route('/{record}/edit'),
        ];
    }
}
