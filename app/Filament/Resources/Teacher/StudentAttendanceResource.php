<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassSchool;
use Filament\Resources\Resource;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\StudentAttendanceResource\Pages;
use App\Filament\Resources\Teacher\StudentAttendanceResource\RelationManagers;
use App\Filament\Resources\Teacher\StudentAttendanceResource\Pages\EditStudentAttendance;
use App\Filament\Resources\Teacher\StudentAttendanceResource\Pages\ListStudentAttendances;
use App\Filament\Resources\Teacher\StudentAttendanceResource\Pages\CreateStudentAttendance;

class StudentAttendanceResource extends Resource
{
    protected static ?string $model = StudentAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'student-attendances';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
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
                TextColumn::make('semester_id')
                    ->label('Semester')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->sortable(),
                ColumnGroup::make('Attendances', [
                    TextInputColumn::make('sick')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric'])
                        ->sortable(),
                    TextInputColumn::make('permission')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric'])
                        ->sortable(),
                    TextInputColumn::make('without_explanation')
                        ->alignment(Alignment::Center)
                        ->rules(['numeric'])
                        ->sortable(),
                ])->alignment(Alignment::Center),
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
            ->actions([])
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
            'index' => Pages\ListStudentAttendances::route('/'),
            // 'create' => Pages\CreateStudentAttendance::route('/create'),
            // 'edit' => Pages\EditStudentAttendance::route('/{record}/edit'),
        ];
    }
}
