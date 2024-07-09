<?php

namespace App\Filament\Resources\MasterData;

use App\Models;
use Filament\Forms;
use Filament\Tables;
use App\Models\Teacher;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Extracurricular;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\MasterData\ExtracurricularExporter;
use App\Filament\Imports\MasterData\ExtracurricularImporter;
use App\Filament\Resources\MasterData\ExtracurricularResource\Pages;
use App\Filament\Resources\MasterData\ExtracurricularResource\RelationManagers;
use App\Filament\Resources\MasterData\ExtracurricularResource\RelationManagers\MemberExtracurricularRelationManager;

class ExtracurricularResource extends Resource
{
    protected static ?string $model = Extracurricular::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')
                    ->relationship('academicYear', 'year')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->label('Teacher')
                    ->options(Teacher::all()->pluck('employee_fullname', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(ExtracurricularExporter::class)
                    ->fileName(fn (Export $export): string => "extracurricular-export-{$export->getKey()}")
                    ->columnMapping(false),
                ImportAction::make()
                    ->importer(ExtracurricularImporter::class),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.employee.fullname')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member_extracurriculars_count')
                    ->label('Total Members')
                    ->badge()
                    ->counts('memberExtracurricular'),
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
            // ->modifyQueryUsing(function (Builder $query) {
            //     $query->whereHas('academicYear', function (Builder $query) {
            //         $query->where('status', true);
            //     });
            // })
            ->filters([
                SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher.employee', 'fullname')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(1)
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

    public static function getRelations(): array
    {
        return [
            MemberExtracurricularRelationManager::class
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('academicYear', function (Builder $query) {
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
            'index' => Pages\ListExtracurriculars::route('/'),
            'create' => Pages\CreateExtracurricular::route('/create'),
            'edit' => Pages\EditExtracurricular::route('/{record}/edit'),
        ];
    }
}
