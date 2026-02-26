<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Pusat Cetak Laporan Reservasi
        </x-slot>

        <x-slot name="description">
            Panduan mencetak dan mengekspor rekapitulasi data transaksi hotel.
        </x-slot>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <p>
                Selamat datang di Pusat Laporan. Di sini Anda dapat mencetak rekapitulasi data reservasi hotel dalam
                format PDF yang sudah distandarisasi untuk keperluan manajemen, evaluasi, dan arsip.
            </p>

            <div>
                <strong>Cara Mencetak Laporan:</strong>
                <ol style="margin-top: 0.5rem; margin-left: 1.5rem; list-style-type: decimal;">
                    <li>Klik tombol <b>"Cetak Laporan (PDF)"</b> di sudut kanan atas halaman ini.</li>
                    <li>Tentukan rentang <b>Tanggal Check-In</b> data yang ingin direkap.</li>
                    <li>Klik tombol <b>Submit</b>.</li>
                </ol>
            </div>

            <hr style="border: 0; border-top: 1px solid rgba(156, 163, 175, 0.3);" />

            <div>
                <strong>💡 Catatan Penting:</strong>
                <ul style="margin-top: 0.5rem; margin-left: 1.5rem; list-style-type: disc;">
                    <li>Hanya reservasi dengan status <b>Confirmed</b> yang akan dihitung masuk ke Total Pendapatan.
                    </li>
                    <li>Format dokumen ini sudah disesuaikan secara otomatis untuk cetak kertas ukuran <b>A4
                            Landscape</b>.</li>
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>