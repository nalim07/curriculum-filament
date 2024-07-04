<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LearningData;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use App\Models\PlanFormatifValue;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PlanFormatifValueTechnique;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\PlanFormatifValueResource\Pages;
use App\Filament\Resources\Teacher\PlanFormatifValueResource\RelationManagers;

class PlanFormatifValueResource extends Resource
{
    protected static ?string $model = PlanFormatifValue::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'plan-formatif-value';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('learning_data_id')
                ->relationship('learningData', 'id', function ($query) {
                    if (auth()->user()->hasRole('super_admin')) {
                        return $query->with('subject')->where('academic_year_id', Helper::getActiveAcademicYearId());
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
                        return $query->with('subject')->where('academic_year_id', Helper::getActiveAcademicYearId());
                    }
                })
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject->name . ' - ' . $record->classSchool->name)
                ->required()
                ->searchable()
                ->live()
                ->preload()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $learningData = LearningData::with('classSchool.level.semester')->find($state);
                    $semesterId = $learningData ? $learningData->classSchool->level->semester->id : null;
                    $termId = $learningData ? $learningData->classSchool->level->term->id : null;
                    $set('semester_id', $semesterId);
                    $set('term_id', $termId);
                })
                ->rules(function ($get) {
                    // $recordId = $get('learning_data_id'); // Assuming 'recordId' is available in the context
                    // return [
                    //     Rule::unique('plan_formatif_values', 'learning_data_id')->ignore($recordId)
                    // ];

                    // Assuming 'plan_formatif_value_id' is available in the context
                    $learningDataId = $get('learning_data_id');
                    $semesterId = $get('semester_id');
                    $termId = $get('term_id');

                    return [
                        Rule::unique('plan_formatif_values', 'learning_data_id')
                            ->where(function ($query) use ($learningDataId, $semesterId, $termId) {
                                return $query
                                    ->where('learning_data_id', $learningDataId)
                                    ->where('semester_id', $semesterId)
                                    ->where('term_id', $termId);
                            }),
                    ];
                })
                ->columnspan('full'),

            Select::make('semester_id')
                ->label('Semester')
                ->default(function (Get $get) {
                    $learningData = LearningData::find($get('learning_data_id'));
                    if ($learningData) {
                        $semester = $learningData->learningData->classSchool->level->semester_id;
                        return $semester ? $semester : null;
                    }
                    return null;
                })
                ->required()
                ->searchable()
                ->preload()
                ->options([
                    '1' => '1',
                    '2' => '2',
                ]),

            Select::make('term_id')
                ->label('Term')
                ->default(function (Get $get) {
                    $learningData = LearningData::find($get('learning_data_id'));
                    if ($learningData) {
                        $term = $learningData->learningData->classSchool->level->term_id;
                        return $term ? $term : null;
                    }
                    return null;
                })
                ->required()
                ->searchable()
                ->preload()
                ->options([
                    '1' => '1',
                    '2' => '2',
                ]),
            Repeater::make('techniques')
                ->relationship('techniques')
                ->schema([
                    TextInput::make('code')->required()->maxLength(255),
                    Select::make('technique')
                        ->options([
                            '1' => 'Parktik',
                            '2' => 'Projek',
                            '3' => 'Produk',
                            '4' => 'Teknik 1',
                            '5' => 'Teknik 2',
                        ])
                        ->required(),
                    TextInput::make('weighting')->label('Minimum Criteria')->required()->numeric()->helperText('Enter a value between 0 and 100')->minValue(0)->maxValue(100),
                ])
                ->minItems(3)
                ->maxItems(3)
                ->addActionLabel('Add Assessment Technique')
                ->columns(3)
                ->columnSpan('full'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('learningData.subject.name')->label('Learning Data')->searchable()->sortable(), Tables\Columns\TextColumn::make('learningData.classSchool.name')->label('Class School')->searchable()->sortable()])
            ->filters(
                [
                    Tables\Filters\SelectFilter::make('learning_data_id')
                        ->label('Learning Data')
                        ->relationship('learningData', 'id', function ($query) {
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
                                return $query->with('subject')->where('academic_year_id', Helper::getActiveAcademicYearId());
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject->name . ' - ' . $record->classSchool->name)
                        ->default(function () {
                            // Fetch the first record based on the same query logic used in the relationship
                            $query = auth()->user()->hasRole('super_admin')
                                ? PlanFormatifValue::with([
                                    'learningData' => function ($query) {
                                        $query->with('subject')->first();
                                    },
                                ])->first()
                                : PlanFormatifValue::whereHas('learningData', function (Builder $query) {
                                    $query
                                        ->with('subject')
                                        ->whereHas('classSchool', function (Builder $query) {
                                            $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                        })
                                        ->where('teacher_id', auth()->user()->employee->teacher->id);
                                })->first();

                            return $query ? $query->learningData->id : null;
                        })
                        ->searchable()
                        ->preload(),

                    Tables\Filters\SelectFilter::make('semester_id')
                        ->label('Semester')
                        ->default(function (Get $get) {
                            $user = Auth::user();
                            $learningDataId = null;
                            if ($user->hasRole('super_admin')) {
                                $learningData = LearningData::with('classSchool.level')->first();
                                if ($learningData) {
                                    $learningDataId = $learningData->id;
                                }
                            } else {
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $learningData = LearningData::with('classSchool.level')
                                        ->where('teacher_id', $user->employee->teacher->id)
                                        ->whereHas('classSchool', function (Builder $query) {
                                            $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                        })->first();
                                    if ($learningData) {
                                        $learningDataId = $learningData->id;
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
                                $learningData = LearningData::with('classSchool.level')->first();
                                if ($learningData) {
                                    $learningDataId = $learningData->id;
                                }
                            } else {
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $learningData = LearningData::with('classSchool.level')
                                        ->where('teacher_id', $user->employee->teacher->id)
                                        ->whereHas('classSchool', function (Builder $query) {
                                            $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                        })->first();
                                    if ($learningData) {
                                        $learningDataId = $learningData->id;
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
            ->filtersFormColumns(3)
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('super_admin')) {
            return parent::getEloquentQuery()->whereHas('learningData.classSchool.academicYear', function (Builder $query) {
                $query->where('id', Helper::getActiveAcademicYearId());
            });
        } else {
            return parent::getEloquentQuery()
                ->whereHas('learningData.classSchool.academicYear', function (Builder $query) {
                    $query->where('id', Helper::getActiveAcademicYearId());
                })->whereHas('learningData.teacher', function (Builder $query) {
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
            'index' => Pages\ListPlanFormatifValues::route('/'),
            'create' => Pages\CreatePlanFormatifValue::route('/create'),
            'edit' => Pages\EditPlanFormatifValue::route('/{record}/edit'),
        ];
    }
}
