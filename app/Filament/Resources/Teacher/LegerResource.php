<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassSchool;
use Filament\Resources\Resource;
use App\Models\MemberClassSchool;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\LegerResource\Pages;
use App\Filament\Resources\Teacher\LegerResource\RelationManagers;

class LegerResource extends Resource
{
    protected static ?string $model = MemberClassSchool::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'leger';

    protected static ?string $modelLabel = 'Leger';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.fullname')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.classSchool.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),
                ColumnGroup::make('Averages', [
                    TextColumn::make('formative_average')
                        ->badge()
                        ->color('primary')
                        ->alignment(Alignment::Center)
                        ->label('Formatif'),
                    TextColumn::make('summative_average')
                        ->badge()
                        ->color('primary')
                        ->alignment(Alignment::Center)
                        ->label('Sumatif'),
                    TextColumn::make('nilai_akhir')
                        ->badge()
                        ->color('primary')
                        ->alignment(Alignment::Center)
                        ->label('Final'),
                ])->alignment(Alignment::Center),
                ColumnGroup::make('Attendances', [
                    TextColumn::make('studentAttendances.sick')
                        ->badge()
                        ->color('primary')
                        ->alignment(Alignment::Center)
                        ->tooltip('Sick')
                        ->label('S'),
                    TextColumn::make('studentAttendances.permission')
                        ->badge()
                        ->color('primary')
                        ->alignment(Alignment::Center)
                        ->tooltip('Permission')
                        ->label('I'),
                    TextColumn::make('studentAttendances.without_explanation')
                        ->badge()
                        ->color('primary')
                        ->alignment(Alignment::Center)
                        ->tooltip('Without Explanation')
                        ->label('A'),
                ])->alignment(Alignment::Center),
                // ColumnGroup::make('Extracurricular', [
                //     ...self::generateExtracurricularColumns(),
                // ])->alignment(Alignment::Center),
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
                        $teacherId = $user->employee->teacher->id;

                        // Fetch the first record based on the same query logic used in the relationship
                        $query = auth()->user()->hasRole('super_admin') ? ClassSchool::whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId())->first() : ClassSchool::whereNotIn('level_id', [1, 2, 3])->where('teacher_id', $teacherId)->where('academic_year_id', Helper::getActiveAcademicYearId())->first();

                        return $query ? $query->id : null;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->deselectAllRecordsWhenFiltered(false)
            ->filtersFormColumns(1)
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // protected static function generateExtracurricularColumns(): array
    // {
    //     $columns = [];
    //     $extracurriculars = \App\Models\Extracurricular::all();

    //     foreach ($extracurriculars as $extracurricular) {
    //         $columns[] = TextColumn::make('extracurricularAssessments.' . $extracurricular->id)
    //             ->label($extracurricular->name)
    //             ->badge()
    //             ->color('primary')
    //             ->alignment(Alignment::Center)
    //             ->getStateUsing(function ($record) use ($extracurricular) {
    //                 $assessment = $record->extracurricularAssessments->firstWhere('extracurricular_id', $extracurricular->id);
    //                 return $assessment ? $assessment->grade : '-';
    //             });
    //     }

    //     return $columns;
    // }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('super_admin')) {
            return parent::getEloquentQuery()->whereHas('classSchool.academicYear', function (Builder $query) {
                $query->where('id', Helper::getActiveAcademicYearId());
            });
        } else {
            return parent::getEloquentQuery()->whereHas('classSchool.academicYear', function (Builder $query) {
                $query->where('id', Helper::getActiveAcademicYearId());
            })->whereHas('classSchool.level.term', function (Builder $query) {
                $query->where('id', Helper::getActiveTermIdPrimarySchool());
            })->whereHas('classSchool.level.semester', function (Builder $query) {
                $query->where('id', Helper::getActiveSemesterIdPrimarySchool());
            })->whereHas('classSchool.teacher', function (Builder $query) {
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
            'index' => Pages\ListLegers::route('/'),
            // 'create' => Pages\CreateLeger::route('/create'),
            // 'edit' => Pages\EditLeger::route('/{record}/edit'),
        ];
    }
}
