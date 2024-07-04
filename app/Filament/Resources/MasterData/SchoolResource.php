<?php

namespace App\Filament\Resources\MasterData;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\School;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MasterData\SchoolResource\Pages;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'School Profile';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        // Get the active academic year
        $activeAcademicYear = AcademicYear::where('status', true)->first();

        return $form
            ->schema([
                Forms\Components\Section::make('School Information')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('academic_year_id')
                                    ->relationship('academicYear', 'year')
                                    ->default($activeAcademicYear ? $activeAcademicYear->id : null)
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Hidden::make('academic_year_id')
                                    ->default($activeAcademicYear ? $activeAcademicYear->id : null),
                                Forms\Components\TextInput::make('school_name')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('npsn')
                                    ->label('NPSN')
                                    ->required()
                                    ->maxLength(10),
                            ]),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('principal')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('nip_principal')
                                    ->label('NIP Principal')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(18),
                            ]),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('nss')
                                    ->label('NSS')
                                    ->maxLength(15),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(35),
                            ]),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('postal_code')
                                    ->required()
                                    ->maxLength(5),

                                Forms\Components\Textarea::make('address')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('website')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('number_phone')
                                    ->tel()
                                    ->maxLength(13),
                            ]),
                    ])->columnSpan([
                        'sm' => 1,
                        'lg' => 2
                    ]),
                Forms\Components\Section::make('Files')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->directory('schools/logo')
                            ->image()
                            ->visibility('public')
                            ->moveFiles()
                            ->downloadable()
                            ->maxSize(2024)
                            ->required()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                $get('npsn') . '.' . $file->getClientOriginalExtension()
                            ),
                        Forms\Components\FileUpload::make('signature_principal')
                            ->directory('schools/signatue_principal')
                            ->image()
                            ->visibility('public')
                            ->moveFiles()
                            ->nullable()
                            ->downloadable()
                            ->maxSize(2024)
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                $get('npsn') . '.' . $file->getClientOriginalExtension()
                            ),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo'),
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('npsn')
                    ->label('NPSN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('principal')
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
            // ->modifyQueryUsing(function (Builder $query) {
            //     $query->whereHas('academicYear', function (Builder $query) {
            //         $query->where('status', true);
            //     });
            // })
            ->filters([
                //
            ])
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
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
