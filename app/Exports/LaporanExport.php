<?php

namespace App\Exports;

use App\Models\Booking;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $awal;
    protected $akhir;
    protected $status;

    public function __construct($awal, $akhir, $status)
    {
        $this->awal = $awal;
        $this->akhir = $akhir;
        $this->status = $status;
    }

    // Menggunakan array agar kita bisa dengan mudah menyisipkan baris Grand Total di bawah
    public function array(): array
    {
        $query = Booking::whereDate('check_in', '>=', $this->awal)
                        ->whereDate('check_in', '<=', $this->akhir);
        
        if ($this->status !== 'semua') {
            $query->where('status', $this->status);
        }

        $bookings = $query->orderBy('check_in', 'asc')->get();

        $data = [];
        $totalPendapatan = 0;

        foreach ($bookings as $b) {
            $data[] = [
                $b->kode_booking ?? '-',
                $b->nama_tamu,
                " " . $b->nomor_hp,
                Carbon::parse($b->check_in)->format('d M Y'),
                Carbon::parse($b->check_out)->format('d M Y'),
                $b->total_harga,
                ucfirst($b->status),
            ];

            // Hanya jumlahkan yang statusnya confirmed
            if ($b->status === 'confirmed') {
                $totalPendapatan += $b->total_harga;
            }
        }

        // --- INI JURUS RAHASIANYA: BARIS GRAND TOTAL ---
        $data[] = [
            'GRAND TOTAL PENDAPATAN (CONFIRMED)', // Akan memakan 5 kolom (di-merge nanti)
            '', '', '', '', 
            $totalPendapatan, 
            ''
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            // Baris 1-3: KOP SURAT (Judul Excel)
            ['LAPORAN DATA RESERVASI HOTEL RUMAH RB'],
            ['Periode: ' . Carbon::parse($this->awal)->format('d M Y') . ' s/d ' . Carbon::parse($this->akhir)->format('d M Y')],
            ['Status Filter: ' . strtoupper($this->status)],
            [], // Baris 4: Kosong (Spasi)
            // Baris 5: Judul Kolom Tabel
            [
                'Kode Booking',
                'Nama Tamu',
                'No. HP',
                'Check In',
                'Check Out',
                'Total Harga',
                'Status',
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"Rp " #,##0',
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn(); 

        // 1. Desain Kop Surat (Merge Kolom A sampai G)
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');

        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A1')->getFont()->setSize(16); // Judul paling besar

        // 2. Desain Header Tabel (Baris 5)
        $sheet->getStyle('A5:G5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // 3. Garis Tabel (Dari baris 5 sampai akhir)
        $sheet->getStyle('A5:G' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // 4. Desain Baris Grand Total (Paling Bawah)
        $sheet->mergeCells('A' . $lastRow . ':E' . $lastRow); // Gabung A sampai E
        $sheet->getStyle('A' . $lastRow . ':G' . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'D9D9D9']], // Abu-abu terang
        ]);
        $sheet->getStyle('A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Teks Grand Total rata kanan

        // 5. Rata Tengah untuk kolom tertentu
        $sheet->getStyle('A6:A' . ($lastRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D6:E' . ($lastRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G6:G' . ($lastRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}