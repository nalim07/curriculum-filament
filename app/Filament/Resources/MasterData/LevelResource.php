<?php

namespace App\Filament\Resources\MasterData;

use Filament\Forms;
use App\Models\Term;
use Filament\Tables;
use App\Models\Level;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Mockery\Matcher\Not;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MasterData\LevelResource\Pages;
use App\Filament\Resources\MasterData\LevelResource\RelationManagers;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Level';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('semester_id')
                    ->disabled(function ($record) {
                        if ($record->school_id == 1) {
                            return true;
                        }
                    })
                    ->relationship('semester', 'semester')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('school_id')
                    ->relationship('school', 'school_name')
                    ->searchable()
                    ->live()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('term_id')
                    ->label('Term')
                    ->options(function (Get $get) {
                        $schoolId = $get('school_id');

                        if ($schoolId == 1) {
                            return [
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                            ];
                        } else {
                            return [
                                1 => '1',
                                2 => '2',
                            ];
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Level Name')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school.school_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Level Name')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('semester_id')
                    ->disabled(function ($record) {
                        if ($record->school_id == 1) {
                            return true;
                        }
                    })
                    ->label('Semester')
                    ->options([
                        '1' => '1',
                        '2' => '2',
                    ])
                    ->selectablePlaceholder(false),
                Tables\Columns\SelectColumn::make('term_id')
                    ->label('Term')
                    ->options(function ($record) {
                        if ($record->school_id != 1) {
                            return [1 => '1', 2 => '2'];
                        } else {
                            return [1 => '1', 2 => '2', 3 => '3', 4 => '4'];
                        }
                    })
                    ->selectablePlaceholder(false),
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
                //
            ])
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

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.master_data");
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLevels::route('/'),
        ];
    }
}
