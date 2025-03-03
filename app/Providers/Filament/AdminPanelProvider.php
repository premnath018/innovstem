<?php

namespace App\Providers\Filament;

use App\Filament\Pages\App\Profile;
use App\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use App\Filament\Pages\Dashboard\AnalyticsDashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Filament\Navigation\MenuItem;
use Filament\Support\Enums\Platform;
use Filament\Enums\ThemeMode;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Nuxtifyts\DashStackTheme\DashStackThemePlugin;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login(Login::class)
            ->passwordReset()
            ->registration()
            ->sidebarCollapsibleOnDesktop()
       //     ->sidebarFullyCollapsibleOnDesktop()
            ->spa()
            ->profile(Profile::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::hex('#fb8b24'),
                'secondary' => Color::hex('#64E3D0'),
            ])
      //      ->topNavigation(true)
            ->globalSearchKeyBindings(['command+s', 'ctrl+s'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL + S',
                Platform::Mac => '⌘ + S',
                default => null,
            })
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->defaultThemeMode(ThemeMode::Dark)
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->databaseNotifications()
            ->pages([
                AnalyticsDashboard::class,
                ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
        //        Widgets\AccountWidget::class,
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
            ])
            ->plugins([
                FilamentSpatieRolesPermissionsPlugin::make(),
                DashStackThemePlugin::make(),
                EasyFooterPlugin::make(),
                FilamentEditProfilePlugin::make()->shouldRegisterNavigation(false)
                ->slug('my-profile')
                ->setTitle('My Profile')
                ->shouldShowDeleteAccountForm(false),
                ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => auth()->user()->name)
                    ->url('/my-profile')
                    ->icon('heroicon-m-user-circle'),
            ]);
    }
}
