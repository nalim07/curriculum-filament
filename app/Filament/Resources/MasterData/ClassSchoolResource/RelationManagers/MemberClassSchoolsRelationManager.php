<?php

namespace App\Filament\Resources\MasterData\ClassSchoolResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Student;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\ClassSchool;
use App\Models\AcademicYear;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MemberClassSchool;
use Filament\Resources\RelationManagers\RelationManager;
use LucasGiovanny\FilamentMultiselectTwoSides\Forms\Components\Fields\MultiselectTwoSides;

class MemberClassSchoolsRelationManager extends RelationManager
{
    protected static string $relationship = 'memberClassSchools';

    protected static ?string $recordTitleAttribute = 'student_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('academic_year_id')->default(AcademicYear::where('status', 1)->first()->id),
                MultiSelectTwoSides::make('student_id')
                    ->options(
                        Student::whereHas('classSchool', function ($query) {
                            $query->where(function ($query) {
                                $query->where('academic_year_id', Helper::getActiveAcademicYearId())
                                    ->whereNull('class_school_id');
                            })->orWhere(function ($query) {
                                $query->where('academic_year_id', '!=', Helper::getActiveAcademicYearId())
                                    ->whereNotNull('class_school_id');
                            });
                        })
                            ->with('classSchool')
                            ->get()
                            ->mapWithKeys(function ($student) {
                                $className = $student->classSchool->name ?? 'No Class';
                                return [$student->id => $student->fullname . ' - ' . $className];
                            })
                            ->toArray(),
                    )
                    ->selectableLabel('Student does not have a class')
                    ->selectedLabel('Selected Student')
                    ->enableSearch(),
                Select::make('registration_type')
                    ->options([
                        '1' => 'Pindahan',
                        '2' => 'Siswa Baru',
                        '3' => 'Naik Kelas',
                        '4' => 'Mengulang',
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->columns('full');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student_id')
            ->columns([
                TextColumn::make('student.nis')->label('NIS')->searchable()->sortable(),
                TextColumn::make('student.fullname')->label('Name')->searchable()->sortable(),
                TextColumn::make('student.gender')->formatStateUsing(fn (string $state): string => Helper::getSex($state))->label('Gender')->searchable()
                    ->sortable(),
                TextColumn::make('registration_type')->formatStateUsing(fn (string $state): string => Helper::getRegistrationType($state))->label('Registration Type')->searchable()->sortable(),
            ])
            // ->modifyQueryUsing(function (Builder $query) {
            //     $query->whereHas('academicYear', function (Builder $query) {
            //         $query->where('status', true);
            //     });
            // })
            ->filters([
                // Define any filters if needed
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
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
}
