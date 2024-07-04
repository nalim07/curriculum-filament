<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Extracurricular;
use Filament\Tables\Columns\TextInputColumn;
use App\Models\ExtracurricularAssessment;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\ExtracurricularAssessmentResource\Pages;
use App\Filament\Resources\Teacher\ExtracurricularAssessmentResource\RelationManagers;

class ExtracurricularAssessmentResource extends Resource
{
    protected static ?string $model = ExtracurricularAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'extracurricular-assessment';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('memberExtracurricular.memberClassSchool.student.fullname')->searchable()->sortable(),
                TextColumn::make('memberExtracurricular.memberClassSchool.student.nis')->label('NIS')->searchable()->sortable(),
                TextColumn::make('memberExtracurricular.memberClassSchool.classSchool.name')->searchable()->sortable(),
                TextColumn::make('memberExtracurricular.extracurricular.name')->searchable()->sortable(),
                SelectColumn::make('grade')
                    ->label('Grade')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                    ])
                    ->searchable()
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        switch ($state) {
                            case 'A':
                                $record->grade = 'A';
                                $record->description = 'Excellent';
                                break;
                            case 'B':
                                $record->grade = 'B';
                                $record->description = 'Good';
                                break;
                            case 'C':
                                $record->grade = 'C';
                                $record->description = 'Fair';
                                break;
                            case 'D':
                                $record->grade = 'D';
                                $record->description = 'Need Improvement';
                                break;
                        }
                        $record->save();
                    }),
                TextInputColumn::make('description')->searchable()->sortable()->disabled(),
            ])
            ->filters(
                [
                    Tables\Filters\SelectFilter::make('extracurricular_id')
                        ->label('Extracurricular')
                        ->relationship('extracurricular', 'id', function ($query) {
                            if (auth()->user()->hasRole('super_admin')) {
                                return $query->with('teacher')->where('academic_year_id', Helper::getActiveAcademicYearId());
                            } else {
                                $user = auth()->user();
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $teacherId = $user->employee->teacher->id;
                                    return $query->with('teacher')
                                        ->where('academic_year_id', Helper::getActiveAcademicYearId())->where('teacher_id', $teacherId);
                                }
                                return $query->with('teacher');
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                        ->searchable()
                        ->preload()
                        ->default(function () {
                            $user = auth()->user();

                            if ($user->hasRole('super_admin')) {
                                $extracurricular = Extracurricular::with('teacher')->first();
                            } else {
                                $extracurricular = Extracurricular::whereHas('teacher', function (Builder $query) use ($user) {
                                    $query->where('academic_year_id', Helper::getActiveAcademicYearId())
                                        ->where('teacher_id', $user->employee->teacher->id);
                                })->first();
                            }

                            return $extracurricular ? $extracurricular->id : null;
                        })
                ],
                layout: FiltersLayout::AboveContent,
            )
            ->deselectAllRecordsWhenFiltered(false)
            ->filtersFormColumns(3)
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('super_admin')) {
            return parent::getEloquentQuery()->whereHas('memberExtracurricular.memberClassSchool.classSchool.academicYear', function (Builder $query) {
                $query->where('id', Helper::getActiveAcademicYearId());
            });
        } else {
            return parent::getEloquentQuery()->whereHas('memberExtracurricular.memberClassSchool.classSchool.academicYear', function (Builder $query) {
                $query->where('id', Helper::getActiveAcademicYearId());
            })->whereHas('memberExtracurricular.memberClassSchool.classSchool.level.term', function (Builder $query) {
                $query->where('id', Helper::getActiveTermIdPrimarySchool());
            })->whereHas('memberExtracurricular.memberClassSchool.classSchool.level.semester', function (Builder $query) {
                $query->where('id', Helper::getActiveSemesterIdPrimarySchool());
            })->whereHas('extracurricular.teacher', function (Builder $query) {
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
        return __("menu.nav_group.report_km");
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtracurricularAssessments::route('/'),
            'create' => Pages\CreateExtracurricularAssessment::route('/create'),
            'edit' => Pages\EditExtracurricularAssessment::route('/{record}/edit'),
        ];
    }
}
