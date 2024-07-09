<?php

namespace App\Filament\Resources\MasterData;

use App\Models;
use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassSchool;
use App\Models\LearningData;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MasterData\LearningDataResource\Pages;
use App\Filament\Resources\MasterData\LearningDataResource\RelationManagers;

class LearningDataResource extends Resource
{
    protected static ?string $model = LearningData::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationLabel = 'Learning Data';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        $activeAcademicYearId = Helper::getActiveAcademicYearId();

        return $form
            ->schema([
                Forms\Components\Select::make('class_school_id')
                    ->label('Class School')
                    ->options(function (Get $get) use ($activeAcademicYearId) {
                        if ($activeAcademicYearId) {
                            return ClassSchool::whereNotIn('level_id', [1, 2, 3])->where('academic_year_id', $activeAcademicYearId)->pluck('name', 'id')->toArray();
                        } else {
                            // Fetch all class school names if there's no active academic year
                            return ClassSchool::where('id', $get('class_school_id'))->pluck('name', 'id')->toArray();
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('subject_id')
                    ->label('Subject')
                    ->options(function (Get $get) use ($activeAcademicYearId) {
                        if ($activeAcademicYearId) {
                            return Subject::where('academic_year_id', $activeAcademicYearId)->pluck('name', 'id')->toArray();
                        } else {
                            // Fetch all subject names if there's no active academic year
                            return Subject::where('id', $get('subject_id'))->pluck('name', 'id')->toArray();
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->label('Teacher')
                    ->options(Teacher::all()->pluck('employee_fullname', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $activeAcademicYearId = Helper::getActiveAcademicYearId();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classSchool.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.employee.fullname')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_school_id')
                    ->label('Class School')
                    ->options(function () use ($activeAcademicYearId) {
                        if ($activeAcademicYearId) {
                            return ClassSchool::where('academic_year_id', $activeAcademicYearId)->pluck('name', 'id')->toArray();
                        } else {
                            // Fetch all class school names if there's no active academic year
                            return ClassSchool::pluck('name', 'id')->toArray();
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->options(function (Get $get) use ($activeAcademicYearId) {
                        if ($activeAcademicYearId) {
                            return Subject::where('academic_year_id', $activeAcademicYearId)->pluck('name', 'id')->toArray();
                        } else {
                            // Fetch all subject names if there's no active academic year
                            return Subject::where('id', $get('subject_id'))->pluck('name', 'id')->toArray();
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher.employee', 'fullname')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->modifyQueryUsing(function ($query) {
                $query->join('class_schools', 'learning_data.class_school_id', '=', 'class_schools.id')
                    ->orderBy('learning_data.status', 'desc')
                    ->orderBy('class_schools.level_id')
                    ->select('learning_data.*');
            })
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('classSchool.academicYear', function (Builder $query) {
            $query->where('status', true);
        });
    }

    public static function getRecord($key): Model
    {
        return static::getEloquentQuery()->findOrFail($key);
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.master_data");
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLearningData::route('/'),
        ];
    }
}
