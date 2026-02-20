<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// --- IMPORT WIDGET BUATAN KITA ---
use App\Filament\Widgets\BookingStatsOverview;
use App\Filament\Widgets\BookingTrendChart;
use App\Filament\Widgets\PopularRoomChart;
use App\Filament\Widgets\UpcomingSchedule;

// --- PENTING: GUNAKAN DASHBOARD CUSTOM (YANG 3 KOLOM) ---
use App\Filament\Pages\Dashboard; 

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('admin')
            ->colors([
                'primary' => Color::Amber,
            ])

            // === 1. GANTI LOGO TAB (FAVICON) DI SINI ===
            ->favicon(asset('img/logo-hotel-1.png')) 

            // === 2. (OPSIONAL) GANTI JUDUL & LOGO DI POJOK KIRI ATAS DASHBOARD ===
            ->brandName('Hotel Rumah RB') 
            ->brandLogo(asset('img/logo-hotel.png')) // Kalau mau pakai gambar logo panjang
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class, // Ini sekarang mengacu ke App\Filament\Pages\Dashboard
            ])
            
            // --- MATIKAN DISCOVER WIDGET AGAR URUTAN SESUAI ---
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            
            ->widgets([
                // Baris 1: Akun (Opsional, kalau mau dihapus boleh)
                // AccountWidget::class, 
                // FilamentInfoWidget::class, // Saya disable biar bersih
                
                // Baris 2: Stats (Otomatis Full)
                BookingStatsOverview::class,
                
                // Baris 3: Chart (Kiri Besar, Kanan Kecil)
                BookingTrendChart::class,
                PopularRoomChart::class,
                
                // Baris 4: Tabel (Full)
                UpcomingSchedule::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}