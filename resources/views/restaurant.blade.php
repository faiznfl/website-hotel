@extends('layouts.main')

@section('title', 'Restoran - Hotel Rumah RB')

@section('content')

    {{-- ========================================== --}}
    {{-- 1. HERO IMAGE HEADER (Gambar di Atas) --}}
    {{-- ========================================== --}}
    <div class="relative w-full h-[250px] sm:h-[300px] md:h-[400px] overflow-hidden">
        <img src="{{ asset('img/hotel-luar.png') }}" alt="Hotel Exterior"
            alt="Restoran Hotel Rumah RB" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

    {{-- ========================================== --}}
    {{-- 2. JUDUL & DESKRIPSI (Di Bawah Gambar) --}}
    {{-- ========================================== --}}
    <div class="bg-white py-12 text-center border-b border-gray-100 shadow-sm relative z-10">
        <div class="max-w-3xl mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-wide uppercase mb-4">Restoran & Dining</h1>
            <div class="w-16 h-1 bg-yellow-500 mx-auto mb-6 rounded-full"></div>
            <p class="text-gray-500 text-base md:text-lg leading-relaxed">
                Menyajikan cita rasa lokal dan internasional terbaik. 
                @auth 
                    Pilih menu favorit Anda, masukkan ke keranjang, dan pesan langsung ke kamar Anda.
                @else
                    Silakan login terlebih dahulu untuk mulai memesan hidangan favorit Anda.
                @endauth
            </p>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 3. MENU GRID SECTION --}}
    {{-- ========================================== --}}
    <div class="bg-gray-50 py-16 min-h-screen relative">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filter Kategori --}}
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button onclick="filterMenu('all')" class="filter-btn active px-5 py-2 rounded-full border border-gray-300 bg-gray-900 text-white text-sm font-medium transition shadow-sm">Semua</button>
                <button onclick="filterMenu('makanan')" class="filter-btn px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium transition shadow-sm">Makanan</button>
                <button onclick="filterMenu('minuman')" class="filter-btn px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium transition shadow-sm">Minuman</button>
                <button onclick="filterMenu('snack')" class="filter-btn px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium transition shadow-sm">Snack</button>
            </div>

            @if($menus->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
                    @foreach($menus as $menu)

                        {{-- CARD ITEM --}}
                        <div class="menu-item bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-yellow-400 transition-all duration-300 flex flex-col h-full group"
                             data-category="{{ $menu->kategori }}">

                            {{-- Gambar Menu --}}
                            <div class="relative h-60 overflow-hidden bg-gray-200">
                                @if($menu->foto)
                                    <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-utensils text-4xl"></i></div>
                                @endif
                                <span class="absolute top-4 left-4 bg-black/70 backdrop-blur text-white text-[10px] font-bold px-3 py-1.5 rounded uppercase tracking-wider">{{ $menu->kategori }}</span>
                            </div>

                            {{-- Info Menu --}}
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug group-hover:text-yellow-600 transition-colors">{{ $menu->nama }}</h3>
                                <p class="text-gray-500 text-sm mb-6 line-clamp-2 flex-grow leading-relaxed">{{ $menu->deskripsi }}</p>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Harga</span>
                                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                    </div>

                                    {{-- LOGIKA TOMBOL (Login vs Guest) --}}
                                    @auth
                                        <button onclick="addToCart({{ $menu->id }}, '{{ $menu->nama }}', {{ $menu->harga }})" 
                                                class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg transition-transform transform active:scale-95 shadow-sm">
                                            <i class="fa-solid fa-plus"></i> Tambah
                                        </button>
                                    @else
                                        <a href="/login" 
                                           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                                            <i class="fa-solid fa-lock"></i> Login
                                        </a>
                                    @endauth

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300 shadow-sm">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-plate-wheat text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Menu belum tersedia</h3>
                    <p class="text-gray-500">Chef kami sedang menyiapkan hidangan spesial untuk Anda.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- FLOATING CART (Hanya muncul jika LOGIN) --}}
    @auth
        <button onclick="toggleCartModal()" 
                class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white p-4 rounded-full shadow-2xl hover:bg-yellow-500 hover:text-gray-900 transition-all duration-300 flex items-center justify-center group">
            <div class="relative">
                <i class="fa-solid fa-basket-shopping text-2xl"></i>
                <span id="cart-count" class="absolute -top-3 -right-3 bg-red-600 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white transform scale-0 transition-transform duration-300 shadow-sm">0</span>
            </div>
            <span class="ml-3 font-bold hidden group-hover:block transition-all whitespace-nowrap">Lihat Pesanan</span>
        </button>

        {{-- CART MODAL --}}
        <div id="cart-modal" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="toggleCartModal()"></div>

            <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl transform transition-transform duration-300 translate-x-full flex flex-col" id="cart-panel">

                <div class="p-6 bg-gray-900 text-white flex justify-between items-center shadow-md">
                    <h2 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-utensils text-yellow-500"></i> Keranjang Pesanan
                    </h2>
                    <button onclick="toggleCartModal()" class="text-gray-400 hover:text-white transition transform hover:rotate-90"><i class="fa-solid fa-xmark text-2xl"></i></button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50" id="cart-items-container">
                    {{-- Diisi Javascript --}}
                </div>

                <div class="p-6 bg-white border-t border-gray-100 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)]">
                    <div class="flex justify-between mb-6 text-xl font-bold text-gray-900">
                        <span>Total Estimasi:</span>
                        <span id="cart-total" class="text-yellow-600">Rp 0</span>
                    </div>

                    {{-- FORM OTOMATIS TERISI DARI AKUN --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4 text-sm">
                        <p class="text-gray-500 mb-1 text-xs uppercase font-bold tracking-wider">Pemesanan atas nama:</p>
                        <p class="font-bold text-gray-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-green-500"></i> {{ auth()->user()->name }}
                        </p>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nomor Kamar</label>
                        <input type="text" id="room-number" placeholder="Contoh: 101" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-yellow-500 focus:outline-none text-sm transition-colors">
                    </div>

                    <button onclick="checkoutWhatsApp()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-green-600/30 flex justify-center items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-xl"></i> Kirim ke Dapur
                    </button>
                </div>
            </div>
        </div>
    @endauth

    {{-- ================= JAVASCRIPT LOGIC ================= --}}
    <script>
        // --- 1. FILTER LOGIC (Public) ---
        function filterMenu(category) {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.display = (category === 'all' || item.dataset.category === category) ? 'flex' : 'none';
            });
            document.querySelectorAll('.filter-btn').forEach(btn => {
                const isActive = btn.onclick.toString().includes(category);
                btn.className = `filter-btn px-5 py-2 rounded-full border text-sm font-medium transition shadow-sm ${isActive ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300'}`;
            });
        }

        // --- 2. CART LOGIC (Only Active if Login) ---
        @auth
            const USER_NAME = "{{ auth()->user()->name }}";
            const USER_EMAIL = "{{ auth()->user()->email }}";

            // Simpan cart berdasarkan email user agar tidak tertukar
            let cart = JSON.parse(localStorage.getItem('hotelMenuCart_' + USER_EMAIL)) || []; 

            function saveCart() {
                localStorage.setItem('hotelMenuCart_' + USER_EMAIL, JSON.stringify(cart));
                updateCartUI();
            }

            function addToCart(id, name, price) {
                const item = cart.find(i => i.id === id);
                if (item) item.qty++; else cart.push({ id, name, price, qty: 1 });
                saveCart();
                const badge = document.getElementById('cart-count');
                badge.classList.add('scale-125'); setTimeout(() => badge.classList.remove('scale-125'), 200);
                if(cart.length === 1) toggleCartModal();
            }

            function removeFromCart(id) {
                cart = cart.filter(item => item.id !== id);
                saveCart();
            }

            function changeQty(id, change) {
                const item = cart.find(item => item.id === id);
                if (item) {
                    item.qty += change;
                    if (item.qty <= 0) removeFromCart(id);
                    else saveCart();
                }
            }

            function updateCartUI() {
                const container = document.getElementById('cart-items-container');
                const totalEl = document.getElementById('cart-total');
                const countEl = document.getElementById('cart-count');
                const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);

                countEl.innerText = totalQty;
                countEl.classList.toggle('scale-0', totalQty === 0);

                if (cart.length === 0) {
                    container.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60 mt-10">
                            <i class="fa-solid fa-basket-shopping text-6xl mb-4"></i>
                            <p class="font-medium">Keranjang masih kosong</p>
                        </div>`;
                    totalEl.innerText = "Rp 0";
                    return;
                }

                let html = '';
                let totalPrice = 0;
                cart.forEach(item => {
                    totalPrice += item.price * item.qty;
                    html += `
                        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex-1 pr-3">
                                <h4 class="font-bold text-gray-900 text-sm mb-1">${item.name}</h4>
                                <span class="text-xs font-bold text-yellow-600">Rp ${new Intl.NumberFormat('id-ID').format(item.price)} <span class="text-gray-400 font-normal">x ${item.qty}</span></span>
                            </div>
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-1.5 border border-gray-100">
                                <button onclick="changeQty(${item.id}, -1)" class="w-7 h-7 rounded bg-white text-gray-600 font-bold shadow-sm hover:bg-gray-100 transition">-</button>
                                <span class="text-sm font-bold w-4 text-center text-gray-900">${item.qty}</span>
                                <button onclick="changeQty(${item.id}, 1)" class="w-7 h-7 rounded bg-gray-900 text-white font-bold shadow-sm hover:bg-gray-800 transition">+</button>
                            </div>
                        </div>`;
                });
                container.innerHTML = html;
                totalEl.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(totalPrice);
            }

            function toggleCartModal() {
                const modal = document.getElementById('cart-modal');
                const panel = document.getElementById('cart-panel');
                if (modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden'); setTimeout(() => panel.classList.remove('translate-x-full'), 10);
                } else {
                    panel.classList.add('translate-x-full'); setTimeout(() => modal.classList.add('hidden'), 300);
                }
            }

            function checkoutWhatsApp() {
                if (cart.length === 0) return alert("Keranjang kosong!");

                const room = document.getElementById('room-number').value.trim();
                if (!room) return alert("Mohon masukkan Nomor Kamar Anda agar kami dapat mengantar pesanan.");

                let message = `*ORDER RESTORAN BARU* 🔔\n`;
                message += `-----------------------------\n`;
                message += `Nama: *${USER_NAME}*\n`; 
                message += `Kamar: *${room}*\n`;
                message += `Status: ✅ User Terverifikasi\n`;
                message += `-----------------------------\n`;
                message += `*Daftar Pesanan:* \n\n`;

                let total = 0;
                cart.forEach(item => {
                    const subtotal = item.price * item.qty;
                    total += subtotal;
                    message += `▪️ ${item.name} \n   ${item.qty} x Rp ${new Intl.NumberFormat('id-ID').format(item.price)}\n`;
                });

                message += `\n-----------------------------\n`;
                message += `*TOTAL ESTIMASI: Rp ${new Intl.NumberFormat('id-ID').format(total)}*`;
                message += `\n-----------------------------\n`;
                message += `_Tolong segera siapkan pesanan ini ya. Terima kasih!_`;

                const encodedMessage = encodeURIComponent(message);
                window.open(`https://wa.me/6281363374155?text=${encodedMessage}`, '_blank');
            }

            // Init Cart UI Logic Only If Auth
            document.addEventListener("DOMContentLoaded", function() {
                updateCartUI();
            });
        @endauth

        // Init Filter Logic Always
        document.addEventListener("DOMContentLoaded", function() {
            filterMenu('all');
        });
    </script>

@endsection