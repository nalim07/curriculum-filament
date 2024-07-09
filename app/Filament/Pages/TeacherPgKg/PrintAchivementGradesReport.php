<?php

namespace App\Filament\Pages\TeacherPgKg;

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

class PrintAchivementGradesReport extends Page
{
    use HasPageShield;

    public ?array $data = [];
    protected ?string $heading = 'Print Report';
    public bool $saveBtn = false;
    public $notes = [];
    public ?Collection $memberClassSchool;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static ?string $navigationLabel = 'Print Report';
    protected static ?string $modelLabel  = 'Print Report';
    protected static string $view = 'filament.pages.teacher.print-pg-kg-report';

    public static function getNavigationSort(): ?int
    {
        return 7;
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
                    Select::make('term_id')
                        ->label('Term')
                        ->searchable()
                        ->options(
                            [
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                            ]
                        )
                        ->required(),
                    Select::make('class_school_id')
                        ->label('Class School')
                        ->searchable()
                        ->options(function (Get $get) {
                            if (auth()->user()->hasRole('super_admin')) {
                                return ClassSchool::whereIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId())->get()->pluck('name', 'id')->toArray();
                            } else {
                                $user = auth()->user();
                                if ($user && $user->employee && $user->employee->teacher) {
                                    $teacherId = $user->employee->teacher->id;
                                    return ClassSchool::whereIn('level_id', [1, 2, 3])->where('teacher_id', $teacherId)->where('academic_year_id', Helper::getActiveAcademicYearId())->get()->pluck('name', 'id')->toArray();
                                }
                                return ClassSchool::whereIn('level_id', [1, 2, 3])->where('academic_year_id', Helper::getActiveAcademicYearId())->get()->pluck('name', 'id')->toArray();
                            }
                        })
                        ->required(),
                    DatePicker::make('date')
                        ->default(now())
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

        return redirect()->route('preview-pg-kg-raport', [
            'livewire' => json_encode($this),
            'data' => json_encode($data)
        ]);
    }

    public function saveData()
    {
        $data = $this->form->getState();

        return redirect()->route('preview-data-pg-kg-raport', [
            'livewire' => json_encode($this),
            'data' => json_encode($data)
        ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.report_tk");
    }
}
