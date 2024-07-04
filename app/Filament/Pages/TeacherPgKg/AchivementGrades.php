<?php

namespace App\Filament\Pages\TeacherPgKg;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TkEvent;
use App\Models\TkPoint;
use App\Models\TkTopic;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\TkElement;

use App\Models\TkSubtopic;
use App\Models\ClassSchool;
use App\Models\TkAttendance;
use Filament\Actions\Action;
use App\Models\HomeroomNotes;
use App\Models\MemberClassSchool;
use App\Models\TkAchivementGrade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use App\Models\PancasilaRaportProject;
use App\Models\StudentPancasilaRaport;
use App\Models\TkAchivementEventGrade;
use function PHPUnit\Framework\isNull;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\PancasilaRaportProjectGroup;
use Illuminate\Database\Eloquent\Collection;
use App\Models\PancasilaRaportValueDescription;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Helpers\GeneratePancasilaRaport; // Pastikan ini sudah diimpor

class AchivementGrades extends Page
{
    // use HasPageShield;
    public ?array $data = [];
    protected ?string $heading = 'Achivement Grades';
    public bool $saveBtn = false;
    public ?int $anggotaKelas;
    public $achivement = [];
    public $notes = [];
    public ?Collection $students;
    public ?Collection $dataTkElements;
    public ?Collection $dataTkTopics;
    public ?Collection $dataTkSubtopics;
    public ?Collection $dataTkPoints;
    public ?Collection $dataEvents;
    public ?Collection $dataAttendance;
    public ?Collection $dataAchivements;
    public ?Collection $dataAchivementEvents;
    public ?Collection $dataCatatanWalikelas;
    public ?MemberClassSchool $dataMemberClassSchool;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.teacher.achivement-grades';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Select::make('term_id')
                        ->label('Term')
                        ->searchable()
                        ->options([
                            1 => '1',
                            2 => '2',
                            3 => '3',
                            4 => '4',
                        ])
                        ->required(),
                    Select::make('class_school_id')
                        ->label('Class School')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->options(
                            ClassSchool::where('academic_year_id', Helper::getActiveAcademicYearId())
                                ->whereIn('level_id', [1, 2, 3])
                                ->get()
                                ->pluck('name', 'id')
                                ->filter(function ($value) {
                                    return !is_null($value); // Remove null values
                                })
                                ->toArray(),
                        )
                        ->required(),
                    Select::make('member_class_school_id')
                        ->label('Student')
                        ->searchable()
                        ->preload()
                        ->options(function (Get $get) {
                            return MemberClassSchool::where('class_school_id', $get('class_school_id'))->get()->pluck('student.fullname', 'id')->toArray();
                        }),
                ])->columns(3),
            ])
            ->statePath('data');
    }

    public function find(): void
    {
        $data = $this->form->getState();

        $classSchool = ClassSchool::where('id', $data['class_school_id'])->first();
        $this->anggotaKelas = $data['member_class_school_id'];

        $this->dataTkElements = TkElement::where('level_id', $classSchool->level->id)->get();
        $this->dataTkTopics = TkTopic::whereIn('tk_element_id', $this->dataTkElements->pluck('id'))->get();
        $this->dataTkSubtopics = TkSubtopic::whereIn('tk_topic_id', $this->dataTkTopics->pluck('id'))->get();
        $this->dataTkPoints = TkPoint::whereIn('tk_topic_id', $this->dataTkTopics->pluck('id'))
            ->where('term_id', $data['term_id'])
            ->get();

        $this->dataAchivements = TkAchivementGrade::where('term_id', $data['term_id'])->get(['member_class_school_id', 'tk_point_id', 'achivement']);
        $this->dataAchivementEvents = TkAchivementEventGrade::get(['member_class_school_id', 'tk_event_id', 'achivement_event']);
        $this->dataAttendance = TkAttendance::where('member_class_school_id', $data['member_class_school_id'])->first(['member_class_school_id', 'no_school_days', 'days_attended', 'days_absent']);
        $this->dataCatatanWalikelas = HomeroomNotes::where('member_class_school_id', $data['member_class_school_id'])->first(['member_class_school_id', 'notes']);

        $this->dataEvents = TkEvent::where('academic_year_id', Helper::getActiveAcademicYearId())
            ->where('term_id', $data['term_id'])
            ->get();

        $this->saveBtn = true;
    }

    public function loadAchievements()
    {
        $achievements = TkAchivementGrade::where('member_class_school_id', $this->data['member_class_school_id'])
            ->where('term_id', $this->data['term_id'])
            ->get();

        foreach ($achievements as $achievement) {
            $this->achivement[$achievement->tk_point_id] = $achievement->achivement;
        }
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            $memberClassSchoolId = $this->data['member_class_school_id'];
            $termId = $this->data['term_id'];

            foreach ($this->achivement as $tkPointId => $achivement) {
                if ($achivement) {
                    TkAchivementGrade::updateOrCreate(
                        [
                            'member_class_school_id' => $memberClassSchoolId,
                            'tk_point_id' => $tkPointId,
                            'term_id' => $termId,
                        ],
                        [
                            'achivement' => $achivement,
                        ]
                    );
                }
            }

            DB::commit();
            Notification::make()
                ->success()
                ->title('Saved!')
                ->send();
        } catch (\Exception $msg) {
            DB::rollBack();
            Notification::make()
                ->danger()
                ->title('Failed!,' . $msg->getMessage())
                ->send();
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.nav_group.report_p5');
    }
}
