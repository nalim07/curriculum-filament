<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\LearningData;
use App\Models\LearningOutcome;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MasterData\TeacherResource;
use App\Filament\Resources\Teacher\PlanSumatifValueResource;
use App\Filament\Resources\Teacher\PlanFormatifValueResource;
use App\Filament\Resources\Teacher\LearningOutcomeResource\Pages;
use App\Filament\Resources\Teacher\LearningOutcomeResource\RelationManagers;

class LearningOutcomeResource extends Resource
{
    protected static ?string $model = LearningOutcome::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?int $navigationSort = -1;

    // change slug
    protected static ?string $slug = 'learning-outcomes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('learning_data_id')
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
                            return $query->with('subject')->whereHas('classSchool', function (Builder $query) {
                                $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                            });
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject->name . ' - ' . $record->classSchool->name)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $learningData = LearningData::with('classSchool.level.semester')->find($state);
                        $semesterId = $learningData ? $learningData->classSchool->level->semester->id : null;
                        $set('semester_id', $semesterId);
                    })
                    ->rules(function ($get) {
                        $recordId = $get('learning_data_id'); // Assuming 'recordId' is available in the context
                        return [Rule::unique('learning_outcomes', 'learning_data_id')->ignore($recordId)];
                    }),
                Hidden::make('semester_id'),
                Repeater::make('learning_outcomes')
                    ->schema([TextInput::make('code')->required()->maxLength(10)->columnSpan(2), Textarea::make('name')->label('Learning Outcome')->required()->maxLength(255), Textarea::make('summary')->required()->maxLength(150)])
                    ->columns(2)
                    ->required() // Ensure that repeater is required
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->collapsible(),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('super_admin')) {
                    return $query;
                } else {
                    $user = auth()->user();
                    if ($user && $user->employee && $user->employee->teacher) {
                        $teacherId = $user->employee->teacher->id;
                        return $query->whereHas('learningData', function (Builder $query) use ($teacherId) {
                            $query->where('teacher_id', $teacherId);
                        });
                    }
                }
            })
            ->columns([Tables\Columns\TextColumn::make('learningData.subject.name')->label('Learning Data')->searchable()->sortable(), Tables\Columns\TextColumn::make('learningData.classSchool.name')->searchable()->sortable(), Tables\Columns\TextColumn::make('learningData.classSchool.level.semester.semester')->searchable()->sortable()])
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
                                return $query->with('subject')->whereHas('classSchool', function (Builder $query) {
                                    $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                                });
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject->name . ' - ' . $record->classSchool->name)
                        ->default(function () {
                            // Fetch the first record based on the same query logic used in the relationship
                            $query = auth()->user()->hasRole('super_admin')
                                ? LearningOutcome::with([
                                    'learningData' => function ($query) {
                                        $query->with('subject')->first();
                                    },
                                ])->first()
                                : LearningOutcome::whereHas('learningData', function (Builder $query) {
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
                ],
                layout: FiltersLayout::AboveContent,
            )
            ->filtersFormColumns(1)
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
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
                })
                ->whereHas('learningData.classSchool.level.term', function (Builder $query) {
                    $query->where('id', Helper::getActiveTermIdPrimarySchool());
                })
                ->whereHas('learningData.classSchool.level.semester', function (Builder $query) {
                    $query->where('id', Helper::getActiveSemesterIdPrimarySchool());
                })
                ->whereHas('learningData.teacher', function (Builder $query) {
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
            'index' => Pages\ListLearningOutcomes::route('/'),
            'create' => Pages\CreateLearningOutcome::route('/create'),
            'edit' => Pages\EditLearningOutcome::route('/{record}/edit'),
        ];
    }
}
