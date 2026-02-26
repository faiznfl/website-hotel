<?php

namespace App\Filament\Pages;

use App\Exports\LaporanExport; 
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
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
                    Select::make('format')
                        ->label('Format Laporan')
                        ->options([
                            'pdf' => 'PDF Dokumen (.pdf)',
                            'excel' => 'Microsoft Excel (.xlsx)',
                        ])
                        ->default('pdf')
                        ->required(),
                    
                    // --- TAMBAHAN BARU: FILTER STATUS ---
                    Select::make('status')
                        ->label('Status Reservasi')
                        ->options([
                            'semua' => 'Semua Status',
                            'confirmed' => 'Confirmed (Selesai/Pasti)',
                            'pending' => 'Pending (Belum Dibayar)',
                            'cancelled' => 'Cancelled (Batal)',
                        ])
                        ->default('semua')
                        ->required(),
                    
                    Select::make('jenis_laporan')
                        ->label('Pilih Rentang Waktu')
                        ->options([
                            'harian' => 'Harian',
                            'bulanan' => 'Bulanan',
                            'rentang' => 'Rentang Tanggal',
                        ])
                        ->required()
                        ->live(),

                    DatePicker::make('tanggal_harian')
                        ->label('Pilih Tanggal')
                        ->required(fn (Get $get) => $get('jenis_laporan') === 'harian')
                        ->visible(fn (Get $get) => $get('jenis_laporan') === 'harian'),

                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options([
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        ])
                        ->required(fn (Get $get) => $get('jenis_laporan') === 'bulanan')
                        ->visible(fn (Get $get) => $get('jenis_laporan') === 'bulanan'),

                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options(function () {
                            $years = [];
                            $currentYear = date('Y');
                            for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                $years[$i] = $i;
                            }
                            return $years;
                        })
                        ->required(fn (Get $get) => $get('jenis_laporan') === 'bulanan')
                        ->visible(fn (Get $get) => $get('jenis_laporan') === 'bulanan'),

                    DatePicker::make('tanggal_awal')
                        ->label('Dari Tanggal')
                        ->required(fn (Get $get) => $get('jenis_laporan') === 'rentang')
                        ->visible(fn (Get $get) => $get('jenis_laporan') === 'rentang'),

                    DatePicker::make('tanggal_akhir')
                        ->label('Sampai Tanggal')
                        ->required(fn (Get $get) => $get('jenis_laporan') === 'rentang')
                        ->visible(fn (Get $get) => $get('jenis_laporan') === 'rentang'),
                ])
                ->action(function (array $data) {
                    $awal = null;
                    $akhir = null;
                    $status = $data['status']; // Tangkap inputan status

                    if ($data['jenis_laporan'] === 'harian') {
                        $awal = $data['tanggal_harian'];
                        $akhir = $data['tanggal_harian'];
                    } elseif ($data['jenis_laporan'] === 'bulanan') {
                        $awal = Carbon::createFromDate($data['tahun'], $data['bulan'], 1)->startOfMonth()->toDateString();
                        $akhir = Carbon::createFromDate($data['tahun'], $data['bulan'], 1)->endOfMonth()->toDateString();
                    } elseif ($data['jenis_laporan'] === 'rentang') {
                        $awal = $data['tanggal_awal'];
                        $akhir = $data['tanggal_akhir'];
                    }

                    if ($data['format'] === 'excel') {
                        // Kirim juga parameter $status ke Excel
                        return Excel::download(new LaporanExport($awal, $akhir, $status), 'Laporan_Reservasi_'.$awal.'_sd_'.$akhir.'.xlsx');
                    }

                    // Kirim juga parameter $status ke PDF
                    return redirect()->route('cetak.laporan.pdf', [
                        'awal' => $awal,
                        'akhir' => $akhir,
                        'status' => $status
                    ]);
                })
        ];
    }
}