<x-filament-panels::page>
    <div
        style="background: #1e293b; padding: 2rem; border-radius: 1rem; color: white; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h1 style="font-size: 1.5rem; font-weight: bold;">Pusat Laporan Terpadu</h1>
        <p style="color: #94a3b8; font-size: 0.875rem;">Sistem Rekapitulasi Data Transaksi Hotel & Restoran — Hotel
            Rumah RB</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

        <div style="background: white; padding: 1.5rem; border-radius: 1rem; border: 1px solid #e2e8f0;">
            <h2
                style="font-weight: bold; margin-bottom: 1.5rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                📋 Panduan Cetak Laporan</h2>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 0.5rem;">🖱️</div>
                    <div>
                        <p style="font-weight: bold; font-size: 0.875rem;">Klik "Cetak Laporan"</p>
                        <p style="font-size: 0.75rem; color: #64748b;">Tombol di pojok kanan atas halaman.</p>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 0.5rem;">🗂️</div>
                    <div>
                        <p style="font-weight: bold; font-size: 0.875rem;">Pilih Kategori</p>
                        <p style="font-size: 0.75rem; color: #64748b;">Pilih Hotel, Restoran, atau Semua.</p>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 0.5rem;">📅</div>
                    <div>
                        <p style="font-weight: bold; font-size: 0.875rem;">Atur Filter</p>
                        <p style="font-size: 0.75rem; color: #64748b;">Tentukan status dan rentang tanggal.</p>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 0.5rem;">📥</div>
                    <div>
                        <p style="font-weight: bold; font-size: 0.875rem;">Download</p>
                        <p style="font-size: 0.75rem; color: #64748b;">Pilih PDF atau Excel lalu Submit.</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 1rem; border-radius: 0.5rem;">
                <p style="font-weight: bold; color: #166534; font-size: 0.75rem; text-transform: uppercase;">💰 Total
                    Pendapatan</p>
                <p style="font-size: 0.875rem; color: #166534; margin-top: 0.25rem;">Hanya status <b>Confirmed</b> &
                    <b>Lunas</b> yang dihitung.</p>
            </div>

            <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 0.5rem;">
                <p style="font-weight: bold; color: #1e40af; font-size: 0.75rem; text-transform: uppercase;">📄 Format
                    Laporan</p>
                <p style="font-size: 0.875rem; color: #1e40af; margin-top: 0.25rem;">Output PDF otomatis berukuran <b>A4
                        Landscape</b>.</p>
            </div>

            <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 1rem; border-radius: 0.5rem;">
                <p style="font-weight: bold; color: #92400e; font-size: 0.75rem; text-transform: uppercase;">💳 Metode
                    Bayar</p>
                <p style="font-size: 0.875rem; color: #92400e; margin-top: 0.25rem;">Info <b>Tunai</b> atau
                    <b>Digital</b> tertera di laporan resto.</p>
            </div>
        </div>

    </div>
</x-filament-panels::page>