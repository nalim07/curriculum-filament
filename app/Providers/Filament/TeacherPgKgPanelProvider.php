<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\User;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Pages\Auth\Login;
use App\Settings\GeneralSettings;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\ThemesPlugin;
use App\Livewire\MyProfileExtended;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Storage;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use App\Filament\Widgets\AcademicYearWidget;
use App\Http\Middleware\CheckPanelPermission;
use App\Filament\Pages\Auth\EmailVerification;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use App\Filament\Pages\Teacher\PancasilaRaport;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\TeacherPgKg\AchivementGrades;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\MasterData\SilabusResource;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Pages\TeacherPgKg\PrintAchivementGradesReport;

class TeacherPgKgPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $canViewThemes = function () {
            if (auth()->check()) {
                $user = User::find(auth()->user()->id);
                return $user->isSuperAdmin();
            } else {
                return false;
            }
        };
        return $panel
            ->id('teacher-pg-kg')
            ->path('teacher-pg-kg')
            ->login(Login::class)
            ->passwordReset(RequestPasswordReset::class)
            ->emailVerification(EmailVerification::class)
            ->favicon(fn (GeneralSettings $settings) => Storage::url($settings->site_favicon))
            ->brandName(fn (GeneralSettings $settings) => $settings->brand_name)
            ->brandLogo(fn (GeneralSettings $settings) => Storage::url($settings->brand_logo))
            ->brandLogoHeight(fn (GeneralSettings $settings) => $settings->brand_logoHeight)
            ->colors(fn (GeneralSettings $settings) => $settings->site_theme)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources/TeacherPgKg'), for: 'App\\Filament\\Resources\\TeacherPgKg')
            ->resources([
                SilabusResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
                AchivementGrades::class,
                PrintAchivementGradesReport::class,
            ])
            ->spa()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                AcademicYearWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->tenantMiddleware([\Hasnayeen\Themes\Http\Middleware\SetTheme::class])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\CheckPanelPermission::class . ':teacher_pg_kg',
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 2,
                        'sm' => 1,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile(shouldRegisterUserMenu: true, shouldRegisterNavigation: false, navigationGroup: 'Settings', hasAvatars: true, slug: 'my-profile')
                    ->myProfileComponents([
                        'personal_info' => MyProfileExtended::class,
                    ]),
                \Hasnayeen\Themes\ThemesPlugin::make()->canViewThemesPage($canViewThemes),
            ]);
    }
}
