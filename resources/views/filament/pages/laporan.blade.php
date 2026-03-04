<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Pusat Laporan Terpadu (Hotel & Restoran)
        </x-slot>

        <x-slot name="description">
            Sistem rekapitulasi data reservasi kamar dan pesanan makanan untuk manajemen.
        </x-slot>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <p>
                Selamat datang di Pusat Laporan. Di sini Anda dapat mengekspor data transaksi baik dari
                <b>Reservasi Hotel</b> maupun <b>Pesanan Restoran</b> dalam format PDF atau Excel.
            </p>

            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                <strong class="text-primary-600">Cara Mencetak Laporan:</strong>
                <ol style="margin-top: 0.5rem; margin-left: 1.5rem; list-style-type: decimal;"
                    class="space-y-1 text-sm">
                    <li>Klik tombol <b>"Cetak Laporan"</b> di sudut kanan atas.</li>
                    <li>Pilih <b>Kategori Data</b> yang diinginkan (Hotel, Restoran, atau Gabungan).</li>
                    <li>Tentukan <b>Status</b> dan <b>Rentang Waktu</b> laporan.</li>
                    <li>Pilih format dokumen (<b>PDF</b> untuk arsip resmi, <b>Excel</b> untuk pengolahan data).</li>
                    <li>Klik tombol <b>Submit</b>.</li>
                </ol>
            </div>

            <hr style="border: 0; border-top: 1px solid rgba(156, 163, 175, 0.3);" />

            <div>
                <strong class="flex items-center gap-2">
                    <span>💡</span> Catatan Penting:
                </strong>
                <ul style="margin-top: 0.5rem; margin-left: 1.5rem; list-style-type: disc;"
                    class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li>
                        <b>Total Pendapatan:</b> Hanya data dengan status <b>Confirmed</b> (Hotel) atau <b>Lunas</b>
                        (Restoran) yang akan dijumlahkan ke dalam Grand Total.
                    </li>
                    <li>
                        <b>Format PDF:</b> Telah dioptimalkan untuk ukuran kertas <b>A4 Landscape</b> agar semua kolom
                        terlihat jelas.
                    </li>
                    <li>
                        <b>Metode Pembayaran:</b> Laporan Restoran akan menyertakan keterangan apakah tamu membayar
                        secara <b>Tunai</b> atau <b>Digital (QRIS/Midtrans)</b>.
                    </li>
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>