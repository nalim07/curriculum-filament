<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\User;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Settings\GeneralSettings;
use App\Filament\Pages\Auth\Login;
use App\Livewire\MyProfileExtended;
use Illuminate\Support\Facades\Storage;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Widgets\AcademicYearWidget;
use App\Filament\Pages\Auth\EmailVerification;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\MasterData\StudentResource;
use App\Filament\Resources\MasterData\SubjectResource;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Resources\MasterData\ClassSchoolResource;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdmissionPanelProvider extends PanelProvider
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
            ->id('admission')
            ->path('admission')
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
            // ->discoverResources(in: app_path('Filament/Resources/MasterData'), for: 'App\\Filament\\Resources\\MasterData')
            ->resources([
                StudentResource::class, // Pastikan resource ini terdaftar di sini
                SubjectResource::class, // Pastikan resource ini terdaftar di sini
                ClassSchoolResource::class, // Pastikan resource ini terdaftar di sini
            ])
            ->pages([Pages\Dashboard::class])
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
                \App\Http\Middleware\CheckPanelPermission::class . ':admission',
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
