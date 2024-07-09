<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SuperAdmin\AgendaResource;
use App\Models\Agenda;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    // view 
    protected static string $view = 'filament.widgets.calendar-widget';

    public Model | string | null $model = Agenda::class;
    protected static ?int $sort = 6;

    public function config(): array
    {
        return [
            'selectable' => true,
            'editable' => true,
            'eventDisplay' => 'block',
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'dayGridWeek  ',
                'center' => 'title',
                'right' => 'prev,next today',
            ],
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Agenda::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Agenda $event) => [
                    'id' => $event->id,
                    'title' => $event->name,
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                ]
            )
            ->all();
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Agenda') // change the label of the button
                ->mountUsing(
                    function (Form $form, array $arguments) {
                        $form->fill([
                            'start_at' => $arguments['start_at'] ?? null, // if a date is selected it will autofill
                            'ends_at' => $arguments['ends_at'] ?? null,
                            'allDay' => true, // the default is an all day event
                        ]);
                    }
                )
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    function (Agenda $record, Form $form, array $arguments) {
                        $form->fill([
                            'title' => $record->name,
                            'start_at' => $record->start_at,
                            'end_at'   => $record->end_at,
                            'allDay' => $record->allDay,
                        ]);
                    }
                ),
            DeleteAction::make(),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->required(),
            Grid::make()
                ->schema([
                    DateTimePicker::make('starts_at')
                        ->required()
                        ->default(now())
                        ->native(false),
                    DateTimePicker::make('ends_at')
                        ->native(false),
                ]),
        ];
    }
}
