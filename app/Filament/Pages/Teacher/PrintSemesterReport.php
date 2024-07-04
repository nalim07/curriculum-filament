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
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DatePicker;

class PrintSemesterReport extends Page
{
    // use HasPageShield;
    public ?array $data = [];
    protected ?string $heading = 'Semester Progress Report';
    public bool $saveBtn = false;
    public $notes = [];
    public ?Collection $memberClassSchool;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static string $view = 'filament.pages.teacher.print-semester-report';

    public static function getNavigationSort(): ?int
    {
        return 7;  // Adjust this number based on where you want this page in the order
    }

    public function mount(): void
    {
        $this->form->fill();
        // $this->pancasilaRaportValueDescription = PancasilaRaportValueDescription::all();
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
                        ->native(false)
                        ->label('Date')
                        ->required(),
                ])->columns(3),
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

        return redirect()->route('preview-semester-raport', [
            'livewire' => json_encode($this),
            'data' => json_encode($data)
        ]);
    }

    public function saveData()
    {
        $data = $this->form->getState();

        return redirect()->route('preview-data-raport', [
            'livewire' => json_encode($this),
            'data' => json_encode($data)
        ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.report_km_homeroom");
    }
}
