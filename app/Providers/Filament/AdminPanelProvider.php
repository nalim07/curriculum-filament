<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Facades\Filament;
use App\Models\EmployeePosition;
use App\Settings\GeneralSettings;
use App\Filament\Pages\Auth\Login;
use Hasnayeen\Themes\ThemesPlugin;
use App\Livewire\MyProfileExtended;
use Filament\Widgets\AccountWidget;
use Filament\Forms\Components\Livewire;
use Illuminate\Support\Facades\Storage;
use Filament\Widgets\FilamentInfoWidget;
use App\Filament\Widgets\AccessLogWidget;
use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Http\Middleware\CheckPanelPermission;
use App\Filament\Pages\Auth\EmailVerification;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Tenancy\EditEmployeePosition;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\MasterData\StudentResource;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Tenancy\RegisterEmployeePosition;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Pages\Tenancy\EditEmployeePositionProfile;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use App\Filament\Widgets\TotalEmployeePositionAndEmployeeUnitChart;
use App\Filament\Resources\MasterData\StudentResource\Widgets\TotalStudentsChart;
use App\Filament\Resources\SuperAdmin\UserResource\Widgets\TotalUserByRolesChart;
use App\Filament\Resources\SuperAdmin\EmployeeResource\Widgets\TotalEmployeesChart;
use App\Filament\Resources\MasterData\ClassSchoolResource\Widgets\TotalMemberClassSchoolInClassSchoolChart;

class AdminPanelProvider extends PanelProvider
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
            ->default()
            ->id('admin')
            ->path('admin')
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
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources/SuperAdmin'), for: 'App\\Filament\\Resources\\SuperAdmin')
            ->discoverResources(in: app_path('Filament/Resources/Shield'), for: 'App\\Filament\\Resources\\Shield')
            ->resources([config('filament-logger.activity_resource')])
            ->discoverPages(in: app_path('Filament/Pages/Auth'), for: 'App\\Filament\\Pages\\Auth')
            ->discoverPages(in: app_path('Filament/Pages/Setting'), for: 'App\\Filament\\Pages\\Setting')
            ->pages([Pages\Dashboard::class])
            ->spa()
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccessLogWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                TotalStudentsChart::class,
                TotalEmployeesChart::class,
                TotalUserByRolesChart::class,
                TotalMemberClassSchoolInClassSchoolChart::class,
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
                \App\Http\Middleware\CheckPanelPermission::class . ':admin',
            ])
            ->plugins([
                \BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin::make(),
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
                FilamentApexChartsPlugin::make(),
            ]);
    }
}
