<?php

namespace App\Filament\Resources\MasterData;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subject;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\MasterData\SubjectExporter;
use App\Filament\Imports\MasterData\SubjectImporter;
use App\Filament\Resources\MasterData\SubjectResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\MasterData\SubjectResource\RelationManagers;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Subjects';

    protected static ?int $navigationSort = 6;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Subject Information')
                    ->description('')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->required()
                            ->default(Helper::getActiveAcademicYearId())
                            ->preload()
                            ->searchable()
                            ->relationship('academicYear', 'year'),
                        Forms\Components\TextInput::make('name')
                            ->label('Subject Name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_idn')
                            ->label('Subject Name (Indonesia)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->helperText('For Slug Religion Subject must start with "religion-"')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\ColorPicker::make('color')
                            ->rgb()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                FilamentExportHeaderAction::make('export'),
                ExportAction::make('export_all')
                    ->label('Export All Data')
                    ->exporter(SubjectExporter::class)
                    ->fileName(fn (Export $export): string => "subject-export-{$export->getKey()}")
                    ->columnMapping(false),
                ImportAction::make()
                    ->importer(SubjectImporter::class),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Subject Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_idn')
                    ->label('Subject Name (Indonesia)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color'),
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
            ->filters([], layout: FiltersLayout::AboveContent)
            ->defaultSort('name')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('Export')
                ]),
            ]);
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.master_data");
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
