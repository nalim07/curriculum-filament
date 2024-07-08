<?php

namespace App\Filament\Resources\MasterData;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Silabus;
use App\Models\Subject;
use App\Models\ClassSchool;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MasterData\SilabusResource\Pages;
use Filament\Tables\Enums\FiltersLayout;

class SilabusResource extends Resource
{
    protected static ?string $model = Silabus::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        $activeAcademicYearId = Helper::getActiveAcademicYearId();

        return $form
            ->schema([
                Section::make('Silabus')
                    ->schema([
                        Forms\Components\Select::make('class_school_id')
                            ->options(function (Get $get) use ($activeAcademicYearId) {
                                if ($activeAcademicYearId) {
                                    return ClassSchool::where('academic_year_id', $activeAcademicYearId)->pluck('name', 'id')->toArray();
                                } else {
                                    return ClassSchool::where('id', $get('class_school_id'))->pluck('name', 'id')->toArray();
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('subject_id')
                            ->options(function (Get $get) use ($activeAcademicYearId) {
                                if ($activeAcademicYearId) {
                                    return Subject::where('academic_year_id', $activeAcademicYearId)->pluck('name', 'id')->toArray();
                                } else {
                                    return Subject::where('id', $get('subject_id'))->pluck('name', 'id')->toArray();
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\FileUpload::make('k_tigabelas')
                            ->label('Kurikulum Tiga Belas')
                            ->directory('students/silabus/k_tigabelas')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(4024)
                            ->downloadable()
                            ->previewable(true)
                            ->moveFiles()
                            ->nullable(),
                        Forms\Components\FileUpload::make('cambridge')
                            ->directory('students/silabus/cambridge')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(4024)
                            ->downloadable()
                            ->previewable(true)
                            ->moveFiles()
                            ->nullable(),
                        Forms\Components\FileUpload::make('edexcel')
                            ->directory('students/silabus/edexcel')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(4024)
                            ->downloadable()
                            ->previewable(true)
                            ->moveFiles()
                            ->nullable(),
                        Forms\Components\FileUpload::make('book_indo_siswa')
                            ->label('Book Indonesian Student')
                            ->directory('students/silabus/book_indo_siswa')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(4024)
                            ->downloadable()
                            ->previewable(true)
                            ->moveFiles()
                            ->nullable(),
                        Forms\Components\FileUpload::make('book_english_siswa')
                            ->label('Book English Student')
                            ->directory('students/silabus/book_english_siswa')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(4024)
                            ->downloadable()
                            ->previewable(true)
                            ->moveFiles()
                            ->nullable(),
                        Forms\Components\FileUpload::make('book_indo_guru')
                            ->label('Book Indonesian Teacher')
                            ->directory('students/silabus/book_indo_guru')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(4024)
                            ->downloadable()
                            ->previewable(true)
                            ->moveFiles()
                            ->nullable(),
                        Forms\Components\FileUpload::make('book_english_guru')
                            ->label('Book English Teacher')
                            ->directory('students/silabus/book_english_guru')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(4024)
                            ->downloadable()
                            ->previewable(true)
                            ->moveFiles()
                            ->nullable(),
                    ])
                    ->columns(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        $activeAcademicYearId = Helper::getActiveAcademicYearId();

        return $table
            ->columns([Tables\Columns\TextColumn::make('classSchool.name')->sortable(), Tables\Columns\TextColumn::make('subject.name')->numeric()->sortable(), Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true), Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)])
            ->filters([
                Tables\Filters\SelectFilter::make('class_school_id')
                    ->label('Class School')
                    ->relationship('classSchool', 'name', function ($query) {
                        if (auth()->user()->hasRole('super_admin')) {
                            return $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                        } else {
                            $user = auth()->user();
                            if ($user && $user->employee && $user->employee->teacher) {
                                $teacherId = $user->employee->teacher->id;
                                return $query->where('teacher_id', $teacherId)->where('academic_year_id', Helper::getActiveAcademicYearId());
                            }
                            return $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()
                    ->preload()
                    ->default(function () {
                        $user = auth()->user();
                        $teacherId = $user->employee->teacher->id;

                        // Fetch the first record based on the same query logic used in the relationship
                        $query = auth()->user()->hasRole('super_admin') ? ClassSchool::where('academic_year_id', Helper::getActiveAcademicYearId())->first() : ClassSchool::where('teacher_id', $teacherId)->where('academic_year_id', Helper::getActiveAcademicYearId())->first();

                        return $query ? $query->id : null;
                    }),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name', function ($query) {
                        if (auth()->user()->hasRole('super_admin')) {
                            return $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                        } else {
                            $user = auth()->user();
                            if ($user && $user->employee && $user->employee->teacher) {
                                return $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                            }
                            return $query->where('academic_year_id', Helper::getActiveAcademicYearId());
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()
                    ->preload()
                    ->default(function () {
                        $user = auth()->user();
                        $teacherId = $user->employee->teacher->id;

                        $query = auth()->user()->hasRole('super_admin') ? Subject::where('academic_year_id', Helper::getActiveAcademicYearId())->first() : Subject::where('academic_year_id', Helper::getActiveAcademicYearId())->first();

                        return $query ? $query->id : null;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('subject.academicYear', function (Builder $query) {
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
        return __('menu.nav_group.master_data');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSilabuses::route('/'),
            'create' => Pages\CreateSilabus::route('/create'),
            'view' => Pages\ViewSilabus::route('/{record}'),
            'edit' => Pages\EditSilabus::route('/{record}/edit'),
        ];
    }
}
