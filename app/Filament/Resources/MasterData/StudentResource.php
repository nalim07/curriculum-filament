<?php

namespace App\Filament\Resources\MasterData;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Helpers\Helper;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassSchool;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Resources\SuperAdmin\UserResource;
use App\Filament\Exports\MasterData\StudentExporter;
use App\Filament\Imports\MasterData\StudentImporter;
use App\Filament\Resources\MasterData\StudentResource\Pages;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')->tabs([
                    Tabs\Tab::make('Student')->schema([
                        self::schoolInformationSection(),
                        self::personalInformationSection(),
                        self::domicileInformationSection(),
                        self::medicalConditionSection(),
                        self::previousEducationSection(),
                    ]),
                    Tabs\Tab::make('Father')->schema([self::fatherInformationSection()]),
                    Tabs\Tab::make('Mother')->schema([self::motherInformationSection()]),
                    Tabs\Tab::make('Guardian')->schema([self::guardianInformationSection()]),
                ]),
            ])
            ->columns('full');
    }

    protected static function schoolInformationSection(): Section
    {
        return Section::make('School Information')
            ->schema([
                Grid::make(3)->schema([
                    Forms\Components\Select::make('class_school_id')
                        ->label('Class')
                        ->searchable()
                        ->preload()
                        ->options(fn (Get $get) => self::getClassSchoolOptions($get)),
                    Forms\Components\Select::make('level_id')
                        ->label('Level')
                        ->searchable()
                        ->preload()
                        ->relationship('level', 'name'),
                    Forms\Components\Select::make('line_id')
                        ->label('Line')
                        ->searchable()
                        ->preload()
                        ->relationship('line', 'name'),
                ]),
                Forms\Components\Select::make('registration_type')
                    ->options(self::getRegistrationTypes())
                    ->required(),
                Forms\Components\TextInput::make('entry_year')->maxLength(255),
                Forms\Components\TextInput::make('entry_semester')->maxLength(255),
                Forms\Components\TextInput::make('entry_class')->maxLength(255),
            ])
            ->columns(2);
    }

    protected static function getClassSchoolOptions(Get $get): array
    {
        $activeAcademicYearId = Helper::getActiveAcademicYearId();
        $classSchool = ClassSchool::where('academic_year_id', $activeAcademicYearId)->pluck('name', 'id')->toArray();

        if ($classSchool) {
            return $classSchool;
        }

        $classSchoolId = $get('class_school_id');
        if ($classSchoolId) {
            return ClassSchool::where('id', $classSchoolId)->pluck('name', 'id')->toArray();
        }

        return [];
    }

    protected static function getRegistrationTypes(): array
    {
        return [
            '1' => 'New Student',
            '2' => 'Transfer Student',
        ];
    }

    protected static function personalInformationSection(): Section
    {
        return Section::make('Personal Information')
            ->description('Student Personal Information')
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User Account')
                    ->relationship('user', 'username')
                    ->options(self::getUserOptions())
                    ->searchable(['username', 'email'])
                    ->preload()
                    ->required()
                    ->rules(self::getUserValidationRules())
                    ->createOptionForm(UserResource::getForm('create') ?? [])
                    ->editOptionForm(UserResource::getForm('edit') ?? []),
                Grid::make(3)->schema([
                    Forms\Components\TextInput::make('nis')
                        ->label('NIS')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('nisn')
                        ->label('NISN')
                        ->maxLength(10),
                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')
                        ->maxLength(16),
                ]),
                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('fullname')
                        ->required()
                        ->maxLength(100),
                ]),
                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('username')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Select::make('gender')
                        ->options(self::getGenderOptions())
                        ->searchable(),
                ]),
                Grid::make(2)->schema([
                    Forms\Components\Select::make('blood_type')
                        ->options(self::getBloodTypeOptions())
                        ->searchable(),
                    Forms\Components\Select::make('religion')
                        ->options(self::getReligionOptions())
                        ->searchable(),
                ]),
                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('place_of_birth')
                        ->maxLength(50),
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->native(false),
                ]),
                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('anak_ke')
                        ->maxLength(2),
                    Forms\Components\TextInput::make('number_of_sibling')
                        ->maxLength(2),
                ]),
                Grid::make(2)->schema([
                    Forms\Components\FileUpload::make('photo')
                        ->label('Pas Photo')
                        ->image()
                        ->directory('students/photos')
                        ->visibility('public')
                        ->maxSize(2024)
                        ->downloadable()
                        ->moveFiles()
                        ->nullable()
                        ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file, Get $get): string => $get('nis') . '.' . $file->getClientOriginalExtension()),
                ]),
            ]);
    }

    protected static function getUserOptions(): callable
    {
        return function () {
            return User::whereDoesntHave('student')
                ->whereDoesntHave('employee')
                ->pluck('username', 'id')
                ->toArray();
        };
    }

    protected static function getUserValidationRules(): callable
    {
        return function (callable $get) {
            $userId = $get('user_id');
            return [
                'exists:users,id',
                Rule::unique('students', 'user_id')->ignore($userId ?? 'null', 'user_id'),
                Rule::unique('employees', 'user_id')->ignore($userId ?? 'null', 'user_id'),
            ];
        };
    }

    protected static function getGenderOptions(): array
    {
        return [
            '1' => 'Male',
            '2' => 'Female',
        ];
    }

    protected static function getBloodTypeOptions(): array
    {
        return [
            'A' => 'A',
            'B' => 'B',
            'AB' => 'AB',
            'O' => 'O',
        ];
    }

    protected static function getReligionOptions(): array
    {
        return [
            '1' => 'Islam',
            '2' => 'Protestan',
            '3' => 'Katolik',
            '4' => 'Hindu',
            '5' => 'Buddha',
            '6' => 'Konghucu',
            '7' => 'Lainnya',
        ];
    }

    protected static function domicileInformationSection(): Section
    {
        return Section::make('Domicile Information')
            ->description('Student Domicile Information')
            ->schema([
                Forms\Components\TextInput::make('citizen')
                    ->helperText('Ex: Indonesia')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('postal_code')
                    ->maxLength(5)
                    ->numeric(),
                Forms\Components\TextInput::make('distance_home_to_school')
                    ->helperText('In KM')
                    ->numeric(),
                Forms\Components\TextInput::make('email_parent')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Select::make('living_together')
                    ->options(self::getLivingTogetherOptions())
                    ->searchable(),
                Forms\Components\TextInput::make('transportation')
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    protected static function getLivingTogetherOptions(): array
    {
        return [
            1 => 'Parents',
            2 => 'Siblings',
        ];
    }

    protected static function medicalConditionSection(): Section
    {
        return Section::make('Medical Condition')
            ->description('Student Medical Condition Information')
            ->schema([
                Forms\Components\TextInput::make('height')->maxLength(255),
                Forms\Components\TextInput::make('weight')->maxLength(255),
                Forms\Components\RichEditor::make('special_treatment')->maxLength(400),
                Forms\Components\RichEditor::make('note_health')->maxLength(400),
                Forms\Components\FileUpload::make('photo_document_health')
                    ->image()
                    ->directory('students/photo_document_health')
                    ->visibility('public')
                    ->maxSize(2024)
                    ->moveFiles()
                    ->nullable()
                    ->downloadable()
                    ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file, Get $get): string => $get('nis') . '.' . $file->getClientOriginalExtension()),
                Forms\Components\FileUpload::make('photo_list_questions')
                    ->image()
                    ->directory('students/photo_list_questions')
                    ->visibility('public')
                    ->maxSize(2024)
                    ->moveFiles()
                    ->nullable()
                    ->downloadable()
                    ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file, Get $get): string => $get('nis') . '.' . $file->getClientOriginalExtension()),
            ])
            ->columns(2);
    }

    protected static function previousEducationSection(): Section
    {
        return Section::make('Previous Education Information')
            ->description('Student Previous Education Information')
            ->schema([
                Forms\Components\DatePicker::make('old_school_entry_date'),
                Forms\Components\DatePicker::make('old_school_leaving_date'),
                Forms\Components\TextInput::make('old_school_name')->maxLength(100),
                Forms\Components\TextInput::make('old_school_achivements')->maxLength(100),
                Forms\Components\TextInput::make('old_school_achivements_year')->maxLength(100),
                Forms\Components\TextInput::make('certificate_number_old_school')->maxLength(100),
                Forms\Components\TextInput::make('old_school_address')->maxLength(100),
                Forms\Components\TextInput::make('no_sttb')->maxLength(255),
                Forms\Components\TextInput::make('nem')->numeric(),
                Forms\Components\FileUpload::make('photo_document_old_school')
                    ->image()
                    ->directory('students/photo_document_old_school')
                    ->visibility('public')
                    ->maxSize(2024)
                    ->moveFiles()
                    ->downloadable()
                    ->nullable()
                    ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file, Get $get): string => $get('nis') . '.' . $file->getClientOriginalExtension()),
            ])
            ->columns(2);
    }

    protected static function fatherInformationSection(): Section
    {
        return Section::make('Father Information')
            ->schema([
                Forms\Components\TextInput::make('nik_father')->maxLength(16),
                Forms\Components\TextInput::make('father_name')->maxLength(100),
                Forms\Components\TextInput::make('father_place_of_birth')->maxLength(100),
                Forms\Components\DatePicker::make('father_date_of_birth')->maxDate(now())->native(false),
                Forms\Components\TextInput::make('father_address')->maxLength(100),
                Forms\Components\TextInput::make('father_phone_number')->tel()->maxLength(13),
                Forms\Components\Select::make('father_religion')
                    ->options(self::getReligionOptions())
                    ->searchable(),
                Forms\Components\TextInput::make('father_city')->maxLength(100),
                Forms\Components\TextInput::make('father_last_education')->maxLength(25),
                Forms\Components\TextInput::make('father_job')->maxLength(100),
                Forms\Components\TextInput::make('father_income')->maxLength(100),
            ])
            ->columns(2);
    }

    protected static function motherInformationSection(): Section
    {
        return Section::make('Mother Information')
            ->schema([
                Forms\Components\TextInput::make('nik_mother')->maxLength(16),
                Forms\Components\TextInput::make('mother_name')->maxLength(100),
                Forms\Components\TextInput::make('mother_place_of_birth')->maxLength(100),
                Forms\Components\DatePicker::make('mother_date_of_birth'),
                Forms\Components\TextInput::make('mother_address')->maxLength(100),
                Forms\Components\TextInput::make('mother_phone_number')->tel()->maxLength(13),
                Forms\Components\Select::make('mother_religion')
                    ->options(self::getReligionOptions())
                    ->searchable(),
                Forms\Components\TextInput::make('mother_city')->maxLength(100),
                Forms\Components\TextInput::make('mother_last_education')->maxLength(25),
                Forms\Components\TextInput::make('mother_job')->maxLength(100),
                Forms\Components\TextInput::make('mother_income')->maxLength(100),
            ])
            ->columns(2);
    }

    protected static function guardianInformationSection(): Section
    {
        return Section::make('Guardian Information')
            ->schema([
                Forms\Components\TextInput::make('nik_guardian')->maxLength(16),
                Forms\Components\TextInput::make('guardian_name')->maxLength(100),
                Forms\Components\TextInput::make('guardian_place_of_birth')->maxLength(100),
                Forms\Components\DatePicker::make('guardian_date_of_birth'),
                Forms\Components\TextInput::make('guardian_address')->maxLength(100),
                Forms\Components\TextInput::make('guardian_phone_number')->tel()->maxLength(13),
                Forms\Components\Select::make('guardian_religion')
                    ->options(self::getReligionOptions())
                    ->searchable(),
                Forms\Components\TextInput::make('guardian_city')->maxLength(100),
                Forms\Components\TextInput::make('guardian_last_education')->maxLength(25),
                Forms\Components\TextInput::make('guardian_job')->maxLength(100),
                Forms\Components\TextInput::make('guardian_income')->maxLength(100),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(StudentExporter::class)
                    ->columnMapping(false)
                    ->authorize(auth()->user()->can('export_student')),
                ImportAction::make()
                    ->importer(StudentImporter::class)
                    ->authorize(auth()->user()->can('import_student')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('fullname')->searchable(),
                Tables\Columns\TextColumn::make('classSchool.name')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('level.name')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('line.name')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('nis')->label('NIS')->searchable(),
                Tables\Columns\TextColumn::make('nisn')->label('NISN')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->deselectAllRecordsWhenFiltered(false)
            ->filtersFormColumns(1)
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.nav_group.master_data');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
