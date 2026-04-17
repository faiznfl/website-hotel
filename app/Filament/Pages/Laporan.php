<?php

namespace App\Filament\Pages;

use App\Exports\LaporanExport; 
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; 

class Laporan extends Page
{
    protected static string| \BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string | \UnitEnum | null $navigationGroup = 'Website & Feedback';
    protected string $view = 'filament.pages.laporan';
    protected static ?int $navigationSort = 8; 

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalWidth('md')
                ->form([
                    // 1. Pilih Kategori Laporan
                    Select::make('kategori_laporan')
                        ->label('Kategori Laporan')
                        ->options([
                            'hotel' => '🏨 Reservasi Hotel',
                            'restoran' => '🍴 Pesanan Restoran',
                            'semua' => '📊 Gabungan (Hotel & Resto)',
                        ])
                        ->default('hotel')
                        ->required()
                        ->live(),

                    Select::make('format')
                        ->label('Format Dokumen')
                        ->options([
                            'pdf' => 'PDF Dokumen (.pdf)',
                            'excel' => 'Microsoft Excel (.xlsx)',
                        ])
                        ->default('pdf')
                        ->required(),
                    
                    // 2. Filter Status (Dinamis sesuai kategori)
                    Select::make('status')
                        ->label('Status Data')
                        ->options(function (Get $get) {
                            $kategori = $get('kategori_laporan');
                            if ($kategori === 'restoran') {
                                return [
                                    'semua' => 'Semua Status Bayar',
                                    'Lunas' => 'Lunas (Sudah Bayar)',
                                    'Belum Bayar' => 'Belum Bayar',
                                ];
                            }
                            return [
                                'semua' => 'Semua Status Reservasi',
                                'confirmed' => 'Confirmed (Lunas)',
                                'pending' => 'Pending (Belum Bayar)',
                                'cancelled' => 'Cancelled (Batal)',
                            ];
                        })
                        ->default('semua')
                        ->required(),
                    
                    Select::make('jenis_laporan')
                        ->label('Rentang Waktu')
                        ->options([
                            'harian' => 'Harian',
                            'bulanan' => 'Bulanan',
                            'rentang' => 'Rentang Tanggal',
                        ])
                        ->required()
                        ->live(),

                    // 3. Input Tanggal Dinamis
                    DatePicker::make('tanggal_harian')
                        ->label('Pilih Tanggal')
                        ->visible(fn (Get $get) => $get('jenis_laporan') === 'harian')
                        ->required(fn (Get $get) => $get('jenis_laporan') === 'harian'),

                    Group::make([
                        Select::make('bulan')
                            ->options(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'])
                            ->label('Bulan')->required(),
                        Select::make('tahun')
                            ->options(array_combine(range(date('Y'), date('Y')-5), range(date('Y'), date('Y')-5)))
                            ->label('Tahun')->required(),
                    ])->columns(2)->visible(fn (Get $get) => $get('jenis_laporan') === 'bulanan'),

                    Group::make([
                        DatePicker::make('tanggal_awal')->label('Mulai Tanggal')->required(),
                        DatePicker::make('tanggal_akhir')->label('Sampai Tanggal')->required(),
                    ])->columns(2)->visible(fn (Get $get) => $get('jenis_laporan') === 'rentang'),
                ])
                ->action(function (array $data) {
                    $awal = null; $akhir = null;
                    $status = $data['status'];
                    $kategori = $data['kategori_laporan'];

                    // Penentuan Range Tanggal
                    if ($data['jenis_laporan'] === 'harian') {
                        $awal = $akhir = $data['tanggal_harian'];
                    } elseif ($data['jenis_laporan'] === 'bulanan') {
                        $awal = Carbon::create($data['tahun'], $data['bulan'], 1)->startOfMonth()->toDateString();
                        $akhir = Carbon::create($data['tahun'], $data['bulan'], 1)->endOfMonth()->toDateString();
                    } else {
                        $awal = $data['tanggal_awal']; $akhir = $data['tanggal_akhir'];
                    }

                    // Eksekusi Export
                    if ($data['format'] === 'excel') {
                        return Excel::download(new LaporanExport($awal, $akhir, $status, $kategori), "Laporan_{$kategori}_{$awal}.xlsx");
                    }

                    return redirect()->route('cetak.laporan.pdf', [
                        'awal' => $awal, 
                        'akhir' => $akhir, 
                        'status' => $status,
                        'kategori' => $kategori
                    ]);
                })
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'manager']);
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'manager']);
    }
}