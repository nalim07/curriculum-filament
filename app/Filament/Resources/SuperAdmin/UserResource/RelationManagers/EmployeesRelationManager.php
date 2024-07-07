<?php

namespace App\Filament\Resources\SuperAdmin\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employee';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id'),
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        // Personal Information
                        Forms\Components\Tabs\Tab::make('Personal Information')
                            ->schema([

                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('fullname')
                                            ->label('Full Name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('employee_code')
                                            ->label('Employee Code')
                                            ->maxLength(25)
                                            ->required(),

                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('nik')
                                            ->label('NIK')
                                            ->maxLength(16)
                                            ->nullable(),
                                        Forms\Components\TextInput::make('citizen')
                                            ->label('Nationality')
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email_school')
                                            ->label('School Email')
                                            ->email()
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Select::make('gender')
                                            ->label('Gender')
                                            ->options([
                                                '1' => 'Male',
                                                '2' => 'Female',
                                            ])
                                            ->required()
                                            ->searchable(),
                                        Forms\Components\Select::make('religion')
                                            ->label('Religion')
                                            ->options([
                                                '1' => 'Islam',
                                                '2' => 'Kristen',
                                                '3' => 'Katolik',
                                                '4' => 'Hindu',
                                                '5' => 'Budha',
                                                '6' => 'Konghucu',
                                                '7' => 'Lainnya',
                                            ])
                                            ->required()
                                            ->searchable(),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('place_of_birth')
                                            ->label('Place of Birth')
                                            ->maxLength(50)
                                            ->nullable(),
                                        Forms\Components\DatePicker::make('date_of_birth')
                                            ->label('Date of Birth')
                                            ->required()
                                            ->native(false),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Select::make('marital_status')
                                            ->label('Marital Status')
                                            ->options([
                                                '1' => 'Merried',
                                                '2' => 'Single',
                                                '3' => 'Widow',
                                                '4' => 'Widower',
                                            ])
                                            ->searchable()
                                            ->nullable(),
                                        Forms\Components\TextInput::make('partner_name')
                                            ->label('Spouse Name')
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('number_of_childern')
                                            ->label('Number of Children')
                                            ->maxLength(255)
                                            ->nullable(),
                                        Forms\Components\TextInput::make('notes')
                                            ->label('Notes')
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                            ]),

                        // Employment Information
                        Forms\Components\Tabs\Tab::make('Employment Information')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Select::make('employee_status_id')
                                            ->label('Employee Status')
                                            ->relationship(name: 'employeeStatus', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\Select::make('employee_unit_id')
                                            ->label('Employee Unit')
                                            ->relationship(name: 'employeeUnit', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Select::make('employee_position_id')
                                            ->label('Employee Position')
                                            ->relationship(name: 'employeePosition', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\DatePicker::make('join_date')
                                            ->label('Join Date')
                                            ->native(false)
                                            ->nullable(),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\DatePicker::make('resign_date')
                                            ->label('Resignation Date')
                                            ->native(false)
                                            ->nullable(),
                                        Forms\Components\DatePicker::make('permanent_date')
                                            ->label('Permanent Date')
                                            ->native(false)
                                            ->nullable(),
                                    ]),
                            ]),
                        // Contact Information
                        Forms\Components\Tabs\Tab::make('Contact Information')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('phone_number')
                                            ->label('Phone Number')
                                            ->tel()
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                                Forms\Components\TextInput::make('address')
                                    ->label('Current Address')
                                    ->maxLength(255)
                                    ->nullable(),
                                Forms\Components\TextInput::make('address_now')
                                    ->label('Permanent Address')
                                    ->maxLength(255)
                                    ->nullable(),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('city')
                                            ->label('City')
                                            ->maxLength(255)
                                            ->nullable(),
                                        Forms\Components\TextInput::make('postal_code')
                                            ->label('Zip Code')
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                            ]),

                        // Tax and Insurance Information
                        Forms\Components\Tabs\Tab::make('Tax and Insurance Information')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('number_npwp')
                                            ->label('Taxpayer Number')
                                            ->maxLength(255)
                                            ->nullable(),
                                        Forms\Components\TextInput::make('name_npwp')
                                            ->label('Taxpayer Name')
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('number_bpjs_ketenagakerjaan')
                                            ->label('BPJS Number')
                                            ->maxLength(255)
                                            ->nullable(),
                                        Forms\Components\TextInput::make('iuran_bpjs_ketenagakerjaan')
                                            ->label('BPJS Iuran')
                                            ->numeric()
                                            ->prefix('Rp. '),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('number_bpjs_yayasan')
                                            ->label('BPJS Number')
                                            ->maxLength(255)
                                            ->nullable(),
                                        Forms\Components\TextInput::make('number_bpjs_pribadi')
                                            ->label('BPJS Number')
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),
                            ]),

                        // Documents
                        Forms\Components\Tabs\Tab::make('Documents')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\FileUpload::make('photo')
                                            ->label('Pas Photo')
                                            ->image()
                                            ->directory('employee/photos')
                                            ->visibility('public')
                                            ->moveFiles()
                                            ->nullable()
                                            ->downloadable()
                                            ->maxSize(2024)
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                                $get('employee_code') . '.' . $file->getClientOriginalExtension()
                                            ),
                                        Forms\Components\FileUpload::make('signature')
                                            ->directory('employee/signature')
                                            ->image()
                                            ->visibility('public')
                                            ->moveFiles()
                                            ->nullable()
                                            ->downloadable()
                                            ->maxSize(2024)
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                                $get('employee_code') . '.' . $file->getClientOriginalExtension()
                                            ),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\FileUpload::make('photo_ktp')
                                            ->label('KTP Photo')
                                            ->directory('employee/photo_ktp')
                                            ->image()
                                            ->visibility('public')
                                            ->moveFiles()
                                            ->nullable()
                                            ->downloadable()
                                            ->maxSize(2024)
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                                $get('employee_code') . '.' . $file->getClientOriginalExtension()
                                            ),
                                        Forms\Components\FileUpload::make('photo_npwp')
                                            ->label('Taxpayer Photo')
                                            ->directory('employee/photo_npwp')
                                            ->image()
                                            ->visibility('public')
                                            ->moveFiles()
                                            ->nullable()
                                            ->downloadable()
                                            ->maxSize(2024)
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                                $get('employee_code') . '.' . $file->getClientOriginalExtension()
                                            ),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\FileUpload::make('photo_kk')
                                            ->label('KK Photo')
                                            ->directory('employee/photo_kk')
                                            ->image()
                                            ->visibility('public')
                                            ->moveFiles()
                                            ->nullable()
                                            ->downloadable()
                                            ->maxSize(2024)
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                                $get('employee_code') . '.' . $file->getClientOriginalExtension()
                                            ),
                                        Forms\Components\FileUpload::make('other_document')
                                            ->label('Other Document')
                                            ->directory('employee/other_document')
                                            ->image()
                                            ->visibility('public')
                                            ->moveFiles()
                                            ->preserveFilenames()
                                            ->nullable()
                                            ->downloadable()
                                            ->maxSize(2024)
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file, Get $get): string =>
                                                $get('employee_code') . '.' . $file->getClientOriginalExtension()
                                            ),
                                    ]),
                            ]),
                    ])->columns(3)->columnSpanFull()
            ]);
    }

    protected function getModalWidth(): string
    {
        return 'full';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fullname')
            ->columns([
                Tables\Columns\TextColumn::make('fullname')->label('Name'),
                Tables\Columns\TextColumn::make('user.username')->label('Username'),
                Tables\Columns\TextColumn::make('user.email')->label('Email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(function (Builder $query): bool {
                        $user = $this->ownerRecord;
                        $user = is_null($user->employee) ? true : false;
                        return $user;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public function canCreate(): bool
    {
        // $user = Auth::user();

        // if($user->employee == null) {
        //     return true;
        // } else if($user->employee != null) {
        //     return false;
        // }

        return false;
    }

    public function CanDeleteRecords(): bool
    {
        return false; // Return false to prevent deletion
    }
}
