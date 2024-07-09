<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Tables\Columns\TexInputtColumn;
use Filament\Forms\Components\Select;
use App\Models\ClassSchool;
use App\Models\GradePromotion;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MemberClassSchool;
use RectorPrefix202308\React\Dns\Model\Record;
use Filament\Tables\Actions\Contracts\HasTable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\GradePromotionResource\Pages;
use App\Filament\Resources\Teacher\GradePromotionResource\RelationManagers;

class GradePromotionResource extends Resource
{
    protected static ?string $model = GradePromotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'grade-promotion';

    public static function form(Form $form): Form
    {
        // Determine the last grade
        $lastGrade = ClassSchool::where('academic_year_id', Helper::getActiveAcademicYearId())->max('level_id');

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
                        ->disabled()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),
                    Select::make('member_class_school_id')
                        ->label('Students')
                        ->disabled()
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
                    Forms\Components\TextInput::make('destination_class')->maxLength(100),
                    // Create the Select component
                    Select::make('decision')
                        ->options(function (Get $get) use ($lastGrade) {
                            $memberClassSchoolId = $get('member_class_school_id');
                            $memberClassSchool = MemberClassSchool::find($memberClassSchoolId);

                            if ($memberClassSchool && $memberClassSchool->classSchool) {
                                $currentLevelId = $memberClassSchool->classSchool->level_id;

                                if ($currentLevelId != $lastGrade) {
                                    return [
                                        '1' => 'Promoted to next grade',
                                        '2' => 'Stay in Class',
                                    ];
                                } else {
                                    return [
                                        '3' => 'Passed',
                                        '4' => 'Not pass',
                                    ];
                                }
                            }

                            return [];
                        })
                        ->searchable()
                        ->preload(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        $lastGrade = ClassSchool::where('academic_year_id', Helper::getActiveAcademicYearId())->max('level_id');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classSchool.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('memberClassSchool.student.fullname')
                    ->sortable(),
                SelectColumn::make('decision')
                    ->options(function (Model $record) use ($lastGrade) {
                        $memberClassSchoolId = $record->member_class_school_id;
                        $memberClassSchool = MemberClassSchool::find($memberClassSchoolId);

                        if ($memberClassSchool && $memberClassSchool->classSchool) {
                            $currentLevelId = $memberClassSchool->classSchool->level_id;

                            if ($currentLevelId != $lastGrade) {
                                return [
                                    '1' => 'Promoted to next grade',
                                    '2' => 'Stay in Class',
                                ];
                            } else {
                                return [
                                    '3' => 'Passed',
                                    '4' => 'Not pass',
                                ];
                            }
                        }

                        return [];
                    })->searchable()->sortable(),
                Tables\Columns\TextInputColumn::make('destination_class')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ], layout: FiltersLayout::AboveContent)
            ->deselectAllRecordsWhenFiltered(false)
            ->filtersFormColumns(1)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            })->whereHas('memberClassSchool.classSchool.level.term', function (Builder $query) {
                $query->where('id', Helper::getActiveTermIdPrimarySchool());
            })->whereHas('memberClassSchool.classSchool.level.semester', function (Builder $query) {
                $query->where('id', Helper::getActiveSemesterIdPrimarySchool());
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
        return __("menu.nav_group.report_km_homeroom");
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradePromotions::route('/'),
            // 'create' => Pages\CreateGradePromotion::route('/create'),
            // 'edit' => Pages\EditGradePromotion::route('/{record}/edit'),
        ];
    }
}
