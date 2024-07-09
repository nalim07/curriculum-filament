<?php

namespace App\Filament\Resources\MasterData;

use GMP;
use App\Models;
use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassSchool;
use App\Models\AcademicYear;
use Filament\Resources\Resource;
use App\Models\MemberClassSchool;
use Tables\Actions\ViewBulkAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\MasterData\ClassSchoolExporter;
use App\Filament\Imports\MasterData\ClassSchoolImporter;
use App\Filament\Resources\MasterData\ClassSchoolResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\MasterData\ClassSchoolResource\RelationManagers;
use LucasGiovanny\FilamentMultiselectTwoSides\Forms\Components\Fields\MultiselectTwoSides;
use App\Filament\Resources\MasterData\ClassSchoolResource\RelationManagers\MemberClassSchoolsRelationManager;

class ClassSchoolResource extends Resource
{
    protected static ?string $model = ClassSchool::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationLabel = 'Class';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Class')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Class Name')
                                    ->required()
                                    ->maxLength(30),
                                Forms\Components\Select::make('level_id')
                                    ->relationship('level', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('line_id')
                                    ->relationship('line', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('teacher_id')
                                    ->label('Teacher')
                                    ->options(Teacher::all()->pluck('employee_fullname', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('academic_year_id')
                                    ->relationship('academicYear', 'year')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ])->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                FilamentExportHeaderAction::make('export'),
                ExportAction::make('export_all')
                    ->label('Export All Data')
                    ->exporter(ClassSchoolExporter::class)
                    ->columnMapping(false)
                    ->authorize(auth()->user()->can('export_class_school')),
                ImportAction::make()
                    ->importer(ClassSchoolImporter::class)
                    ->authorize(auth()->user()->can('import_class_school')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Class Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('line.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('teacher.employee.fullname')
                    ->label('Homeroom Teacher')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('student_count')
                    ->label('Total Student')
                    ->badge()
                    ->counts('student'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->modifyQueryUsing(function (Builder $query) {
            //     $query->whereHas('academicYear', function (Builder $query) {
            //         $query->where('status', true);
            //     });
            // })
            ->filters([
                Tables\Filters\SelectFilter::make('level_id')
                    ->label('Level')
                    ->relationship('level', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('line_id')
                    ->label('Line')
                    ->relationship('line', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Homeroom Teacher')
                    ->relationship('teacher.employee', 'fullname')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MemberClassSchoolsRelationManager::class
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('academicYear', function (Builder $query) {
            $query->where('status', true);
        })->orderBy('level_id', 'asc');
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
            'index' => Pages\ListClassSchools::route('/'),
            'create' => Pages\CreateClassSchool::route('/create'),
            'edit' => Pages\EditClassSchool::route('/{record}/edit'),
            'view' => Pages\ViewClassSchool::route('/{record}'),
        ];
    }
}
