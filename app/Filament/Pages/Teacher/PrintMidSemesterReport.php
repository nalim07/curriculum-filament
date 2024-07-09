<?php

namespace App\Filament\Pages\Teacher;

use App\Helpers\Helper;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\ClassSchool;
use App\Models\MemberClassSchool;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Collection;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class PrintMidSemesterReport extends Page
{
    // use HasPageShield;
    public ?array $data = [];
    protected ?string $heading = 'Mid-Semester Progress Raport';
    public bool $saveBtn = false;
    public $notes = [];
    public ?Collection $memberClassSchool;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static string $view = 'filament.pages.teacher.print-mid-semester-report';

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Select::make('class_school_id')
                        ->label('Class School')
                        ->searchable()
                        ->options(function (Get $get) {
                            return ClassSchool::where('academic_year_id', Helper::getActiveAcademicYearId())->whereNotIn('level_id', [1, 2, 3])->get()->pluck('name', 'id')->toArray();
                        })
                        ->required(),
                    Select::make('term_id')
                        ->label('Term')
                        ->searchable()
                        ->options(
                            [
                                2 => '2',
                            ]
                        )
                        ->required(),
                    Select::make('semester_id')
                        ->label('Semester')
                        ->searchable()
                        ->options(
                            [
                                1 => '1',
                                2 => '2',
                            ]
                        )
                        ->required(),
                    DatePicker::make('date')
                        ->default(now())
                        ->native(false)
                        ->label('Date')
                        ->required(),
                ])->columns(2),
            ])
            ->statePath('data');
    }

    public function find(): void
    {
        $data = $this->form->getState();

        $studentIDs = MemberClassSchool::where('class_school_id', $data['class_school_id'])
            ->pluck('student_id');

        $this->memberClassSchool = Student::whereIn('id', $studentIDs)
            ->where('class_school_id', $data['class_school_id'])
            ->get();

        $this->saveBtn = true;
    }

    public function save()
    {
        $data = $this->form->getState();

        return redirect()->route('preview-mid-semester-raport', [
            'livewire' => json_encode($this),
            'data' => json_encode($data)
        ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.report_km_homeroom");
    }
}
