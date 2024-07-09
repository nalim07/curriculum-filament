<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Models\Level;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassSchool;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use App\Models\MemberClassSchool;
use App\Models\StudentAchievement;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\StudentAchievementResource\Pages;
use App\Filament\Resources\Teacher\StudentAchievementResource\RelationManagers;

class StudentAchievementResource extends Resource
{
    protected static ?string $model = StudentAchievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'student-achievement';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Student Achievement Information')
                ->description('')
                ->schema([
                    Forms\Components\Select::make('class_school_id')
                        ->relationship('classSchool', 'name', function ($query) {
                            if (auth()->user()->hasRole('super_admin')) {
                                return $query->whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId())->orderBy('level_id');
                            } else {
                                $user = auth()->user();
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $teacherId = $user->employee->teacher->id;
                                    return $query->whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId())->orderBy('level_id')->where('teacher_id', $teacherId);
                                }
                                return $query->whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId());
                            }
                        })
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            $classSchool = ClassSchool::find($state);
                            if ($classSchool) {
                                $set('semester_id', $classSchool->level->semester_id);
                            }
                        })
                        ->required(),
                    Forms\Components\Select::make('semester_id')
                        ->label('Semester')
                        ->options([
                            '1' => '1',
                            '2' => '2',
                        ])
                        ->default(function (Get $get) {
                            $classSchool = ClassSchool::find($get('class_school_id'));
                            if ($classSchool) {
                                $semester = $classSchool->level->semester_id;
                                return $semester ? $semester : null;
                            }
                            return null;
                        })
                        ->searchable()
                        ->required(),
                    Select::make('member_class_school_id')
                        ->label('Students')
                        ->options(function (Get $get) {
                            $selectedClassSchool = ClassSchool::find($get('class_school_id'));

                            if ($selectedClassSchool) {
                                if ($selectedClassSchool->memberClassSchools) {
                                    $memberClassSchool = $selectedClassSchool->memberClassSchools->pluck('id')->toArray();

                                    return MemberClassSchool::whereIn('id', $memberClassSchool)->get()->pluck('student.fullname', 'id');
                                }
                            }

                            return collect();
                        })
                        ->searchable()
                        ->columns(3),
                    Forms\Components\TextInput::make('name')->required()->maxLength(100),
                    Forms\Components\Select::make('type_of_achievement')
                        ->options([
                            '1' => 'Academic',
                            '2' => 'Non-Academic',
                        ])
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('level_achievement')
                        ->searchable()
                        ->options([
                            '1' => 'International',
                            '2' => 'National',
                            '3' => 'Province',
                            '4' => 'City',
                            '5' => 'District',
                            '6' => 'Inter School',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('description')->required()->maxLength(200),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('memberClassSchool.student.fullname')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('memberClassSchool.classSchool.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type_of_achievement')
                    ->formatStateUsing(fn (string $state) => Helper::getTypeOfAchievement($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level_achievement')
                    ->formatStateUsing(fn (string $state) => Helper::getLevelOfAchievement($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_school_id')
                    ->label('Class School')
                    ->relationship('classSchool', 'name', function ($query) {
                        if (auth()->user()->hasRole('super_admin')) {
                            return $query->whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId());
                        } else {
                            $user = auth()->user();
                            if ($user && $user->employee && $user->employee->teacher) {
                                $teacherId = $user->employee->teacher->id;
                                return $query->whereNotIn('level_id', [1, 2, 3])->where('teacher_id', $teacherId)->where('academic_year_id', Helper::getActiveAcademicYearId());
                            }
                            return $query->whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId());
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()
                    ->preload()
                    ->default(function () {
                        $user = auth()->user();

                        // Fetch the first record based on the same query logic used in the relationship
                        $query = auth()->user()->hasRole('super_admin') ? ClassSchool::whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId())->first() : ClassSchool::whereNotIn('level_id', [1, 2, 3])->where('teacher_id', $user->employee->teacher->id)->where('academic_year_id', Helper::getActiveAcademicYearId())->first();

                        return $query ? $query->id : null;
                    }),
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->default(function (Get $get) {
                        $user = Auth::user();
                        if ($user->hasRole('super_admin')) {
                            $classSchool = ClassSchool::whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId())->first();

                            return $classSchool->level->semester->id ?? null;
                        } else {
                            if ($user && $user->employee && $user->employee->teacher) {
                                $classSchool = ClassSchool::whereNotIn('level_id', [1, 2, 3])->where('teacher_id', $user->employee->teacher->id)->where('academic_year_id', Helper::getActiveAcademicYearId())->first();
                                if ($classSchool) {
                                    return $classSchool->level->semester->id ?? null;
                                }
                            }
                        }

                        // return null;
                    })
                    ->options([
                        '1' => '1',
                        '2' => '2',
                    ])
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->deselectAllRecordsWhenFiltered(false)
            ->filtersFormColumns(2)
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ])
            ]);
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
            })->whereHas('memberClassSchool.classSchool.teacher', function (Builder $query) {
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
        return __('menu.nav_group.report_km_homeroom');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentAchievements::route('/'),
            'create' => Pages\CreateStudentAchievement::route('/create'),
            'edit' => Pages\EditStudentAchievement::route('/{record}/edit'),
        ];
    }
}
