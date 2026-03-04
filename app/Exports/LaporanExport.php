<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromArray, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $awal;
    protected $akhir;
    protected $status;
    protected $kategori;

    public function __construct($awal, $akhir, $status, $kategori)
    {
        $this->awal = $awal;
        $this->akhir = $akhir;
        $this->status = $status;
        $this->kategori = $kategori;
    }

    public function array(): array
    {
        $data = [];
        $totalPendapatan = 0;

        // Kop Surat
        $data[] = ['LAPORAN OPERASIONAL TERPADU - RUMAH RB'];
        $data[] = ['Periode: ' . Carbon::parse($this->awal)->format('d M Y') . ' s/d ' . Carbon::parse($this->akhir)->format('d M Y')];
        $data[] = ['Kategori: ' . strtoupper($this->kategori)];
        $data[] = []; 

        // Data Hotel
        if ($this->kategori === 'hotel' || $this->kategori === 'semua') {
            $bookings = Booking::whereDate('check_in', '>=', $this->awal)
                ->whereDate('check_in', '<=', $this->akhir)
                ->when(($this->status !== 'semua' && $this->kategori === 'hotel'), fn($q) => $q->where('status', $this->status))
                ->get();

            $data[] = ['A. DATA RESERVASI HOTEL'];
            $data[] = ['Kode Booking', 'Nama Tamu', 'No. HP', 'Check In', 'Check Out', 'Total Harga', 'Status'];

            foreach ($bookings as $b) {
                $data[] = [
                    $b->kode_booking,
                    $b->nama_tamu,
                    " " . $b->nomor_hp,
                    $b->check_in,
                    $b->check_out,
                    (float)$b->total_harga,
                    ucfirst($b->status),
                ];
                if ($b->status === 'confirmed') $totalPendapatan += $b->total_harga;
            }
            $data[] = []; $data[] = []; 
        }

        // Data Resto
        if ($this->kategori === 'restoran' || $this->kategori === 'semua') {
            $orders = Order::whereDate('created_at', '>=', $this->awal)
                ->whereDate('created_at', '<=', $this->akhir)
                ->when(($this->status !== 'semua' && $this->kategori === 'restoran'), fn($q) => $q->where('status_pembayaran', $this->status))
                ->get();

            $data[] = ['B. DATA PESANAN RESTORAN'];
            $data[] = ['ID Order', 'Nama Customer', 'Lokasi/Kamar', 'Waktu Pesan', 'Metode Bayar', 'Total Harga', 'Status'];

            foreach ($orders as $o) {
                $data[] = [
                    'FOOD-' . $o->id,
                    $o->nama_pemesan,
                    $o->info_pemesan,
                    $o->created_at->format('d/m/Y H:i'),
                    $o->metode_pembayaran ?? 'cash',
                    (float)$o->total_harga,
                    $o->status_pembayaran,
                ];
                if ($o->status_pembayaran === 'Lunas') $totalPendapatan += $o->total_harga;
            }
            $data[] = [];
        }

        // Grand Total
        $data[] = ['RINGKASAN PENDAPATAN AKHIR'];
        $data[] = ['TOTAL PENDAPATAN BERSIH (HOTEL CONFIRMED + RESTO LUNAS)', '', '', '', '', (float)$totalPendapatan, ''];

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // 1. STYLING KOP SURAT (Baris 1-3)
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A3')->getFont()->setItalic(true)->setSize(11);

        // 2. MENCARI BARIS HEADER SECARA DINAMIS
        // Kita cari teks "A. DATA" dan "B. DATA" untuk dikasih warna
        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            $cellValue = $sheet->getCell('A' . $rowIndex)->getValue();

            // Style untuk Sub-Judul Seksi (A. DATA... / B. DATA...)
            if (str_contains($cellValue, 'DATA RESERVASI') || str_contains($cellValue, 'DATA PESANAN')) {
                $sheet->mergeCells("A{$rowIndex}:G{$rowIndex}");
                $sheet->getStyle("A{$rowIndex}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '2C3E50']],
                ]);
            }

            // Style untuk Header Kolom (Tepat di bawah Sub-Judul)
            if ($cellValue == 'Kode Booking' || $cellValue == 'ID Order') {
                $sheet->getStyle("A{$rowIndex}:G{$rowIndex}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'ECF0F1']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
            
            // Style untuk Judul Ringkasan
            if (str_contains($cellValue, 'RINGKASAN')) {
                $sheet->mergeCells("A{$rowIndex}:G{$rowIndex}");
                $sheet->getStyle("A{$rowIndex}")->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle("A{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // 3. STYLING BARIS TOTAL AKHIR (Baris Paling Bawah)
        $sheet->mergeCells('A' . $lastRow . ':E' . $lastRow);
        $sheet->getStyle('A' . $lastRow . ':G' . $lastRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '27AE60']], // Hijau Sukses
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($lastRow)->setRowHeight(25); // Bikin baris total lebih tinggi/lega

        // 4. GLOBAL BORDER & ALIGNMENT
        // Berikan border tipis ke seluruh area tabel dari baris 5
        $sheet->getStyle('A5:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Kolom Harga (F) selalu rata kanan
        $sheet->getStyle('F6:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Kolom Status (G) rata tengah
        $sheet->getStyle('G6:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    // INI DIA: Nama fungsinya harus "columnFormats" (pakai S)
    public function columnFormats(): array
    {
        return [
            'F' => '"Rp " #,##0', 
        ];
    }
}