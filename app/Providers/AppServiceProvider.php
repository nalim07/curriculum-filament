<?php

namespace App\Providers;

use Livewire\Livewire;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Tables\Actions\Action;
use Illuminate\Support\ServiceProvider;
use App\Filament\Widgets\CalendarWidget;
use Filament\Navigation\NavigationGroup;
use Filament\Tables\Enums\FiltersLayout;
use BezhanSalleh\PanelSwitch\PanelSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table
                ->emptyStateHeading('No data nyet')
                ->striped()
                ->defaultPaginationPageOption(10)
                ->paginated([10, 25, 50, 100])
                ->extremePaginationLinks()
                ->defaultSort('created_at', 'desc');
        });

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->modalHeading('Available Panels')
                ->modalWidth('sm')
                ->slideOver()
                ->icons([
                    'student' => 'heroicon-o-user',
                    'curriculum' => 'heroicon-o-circle-stack',
                    'admission' => 'heroicon-o-clipboard-document-list',
                    'teacher' => 'heroicon-o-user-group',
                    'teacher-pg-kg' => 'heroicon-o-users',
                    'admin' => 'heroicon-o-cog-6-tooth',
                ])
                ->iconSize(15)
                ->labels([
                    'student' => 'Student',
                    'curriculum' => 'Curriculum',
                    'admission' => 'Admission',
                    'teacher' => 'Teacher',
                    'teacher-pg-kg' => 'Teacher PG-KG',
                    'admin' => 'Admin',
                ]);

            $panelSwitch->excludes(function () {
                $user = auth()->user();

                if ($user) {
                    $excludedPanels = [];

                    if (!$user->can('can_access_panel_student')) {
                        $excludedPanels[] = 'student';
                    }

                    if (!$user->can('can_access_panel_curriculum')) {
                        $excludedPanels[] = 'curriculum';
                    }

                    if (!$user->can('can_access_panel_admission')) {
                        $excludedPanels[] = 'admission';
                    }

                    if (!$user->can('can_access_panel_teacher')) {
                        $excludedPanels[] = 'teacher';
                    }

                    if (!$user->can('can_access_panel_teacher_pg_kg')) {
                        $excludedPanels[] = 'teacher-pg-kg';
                    }

                    if (!$user->can('can_access_panel_admin')) {
                        $excludedPanels[] = 'admin';
                    }

                    return $excludedPanels;
                }

                // User yang tidak terautentikasi tidak dapat mengakses panel manapun
                return ['admin', 'curriculum', 'admission', 'teacher', 'teacher_pg_kg', 'student'];
            });
        });
    }
}
