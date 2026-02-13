@extends('layouts.main')

@section('title', 'Restoran - Hotel Rumah RB')

@section('content')

    {{-- 1. PAGE TITLE HEADER --}}
    <div class="bg-gray-900 py-16 text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fbbf24 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="relative z-10 max-w-2xl mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold tracking-wide uppercase mb-2">Restoran & Dining</h1>
            <div class="w-16 h-1 bg-yellow-500 mx-auto mb-4"></div>
            <p class="text-gray-400 text-sm md:text-base">
                Menyajikan cita rasa lokal dan internasional terbaik.
            </p>
        </div>
    </div>

    {{-- 2. MENU GRID --}}
    <div class="bg-gray-50 py-16 min-h-screen relative">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filter Kategori --}}
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button onclick="filterMenu('all')" class="filter-btn active px-5 py-2 rounded-full border border-gray-300 bg-gray-900 text-white text-sm font-medium transition">Semua</button>
                <button onclick="filterMenu('makanan')" class="filter-btn px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium transition">Makanan</button>
                <button onclick="filterMenu('minuman')" class="filter-btn px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium transition">Minuman</button>
                <button onclick="filterMenu('snack')" class="filter-btn px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium transition">Snack</button>
            </div>

            @if($menus->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
                    @foreach($menus as $menu)
                        <div class="menu-item bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl hover:border-yellow-400 transition-all duration-300 flex flex-col h-full group"
                             data-category="{{ $menu->category }}">
                            
                            {{-- Gambar Menu --}}
                            <div class="relative h-60 overflow-hidden bg-gray-200">
                                @if($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-utensils text-4xl"></i></div>
                                @endif
                                <span class="absolute top-3 left-3 bg-black/70 backdrop-blur text-white text-[10px] font-bold px-3 py-1 rounded uppercase tracking-wider">{{ $menu->category }}</span>
                            </div>

                            {{-- Info Menu --}}
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-1 leading-snug">{{ $menu->name }}</h3>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2 flex-grow">{{ $menu->description }}</p>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                    
                                    {{-- LOGIKA TOMBOL (Login vs Guest) --}}
                                    @auth
                                        {{-- Jika Login: Muncul Tombol Tambah --}}
                                        <button onclick="addToCart({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }})" 
                                                class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition-transform transform active:scale-95 shadow-sm">
                                            <i class="fa-solid fa-plus"></i> Tambah
                                        </button>
                                    @else
                                        {{-- Jika Tamu: Muncul Tombol Login --}}
                                        <a href="/login" 
                                           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-500 text-xs font-bold px-4 py-2 rounded-lg transition">
                                            <i class="fa-solid fa-lock"></i> Login
                                        </a>
                                    @endauth

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                    Menu belum tersedia.
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
                <span id="cart-count" class="absolute -top-3 -right-3 bg-red-600 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white transform scale-0 transition-transform duration-300">0</span>
            </div>
            <span class="ml-3 font-bold hidden group-hover:block transition-all">Lihat Pesanan</span>
        </button>

        {{-- CART MODAL --}}
        <div id="cart-modal" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="toggleCartModal()"></div>

            <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl transform transition-transform duration-300 translate-x-full flex flex-col" id="cart-panel">
                
                <div class="p-5 bg-gray-900 text-white flex justify-between items-center shadow-md">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fa-solid fa-utensils text-yellow-500"></i> Keranjang Pesanan
                    </h2>
                    <button onclick="toggleCartModal()" class="text-gray-400 hover:text-white transition"><i class="fa-solid fa-xmark text-2xl"></i></button>
                </div>

                <div class="flex-1 overflow-y-auto p-5 space-y-4" id="cart-items-container">
                    {{-- Diisi Javascript --}}
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between mb-4 text-xl font-bold text-gray-900">
                        <span>Total Estimasi:</span>
                        <span id="cart-total">Rp 0</span>
                    </div>

                    {{-- FORM OTOMATIS TERISI DARI AKUN --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4 shadow-sm text-sm">
                        <p class="text-gray-500 mb-1">Pemesanan atas nama:</p>
                        <p class="font-bold text-gray-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-green-500"></i> {{ auth()->user()->name }}
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <input type="text" id="room-number" placeholder="Masukkan Nomor Kamar Anda" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none text-sm">
                    </div>

                    <button onclick="checkoutWhatsApp()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg flex justify-center items-center gap-2">
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
                btn.className = `filter-btn px-5 py-2 rounded-full border text-sm font-medium transition ${isActive ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300'}`;
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
                    container.innerHTML = `<div class="text-center text-gray-400 mt-10"><p>Keranjang kosong.</p></div>`;
                    totalEl.innerText = "Rp 0";
                    return;
                }

                let html = '';
                let totalPrice = 0;
                cart.forEach(item => {
                    totalPrice += item.price * item.qty;
                    html += `
                        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-sm mb-1">${item.name}</h4>
                                <span class="text-xs font-semibold text-yellow-600">Rp ${new Intl.NumberFormat('id-ID').format(item.price)} x ${item.qty}</span>
                            </div>
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-1">
                                <button onclick="changeQty(${item.id}, -1)" class="w-7 h-7 rounded-md bg-white text-gray-600 font-bold">-</button>
                                <span class="text-sm font-bold w-4 text-center">${item.qty}</span>
                                <button onclick="changeQty(${item.id}, 1)" class="w-7 h-7 rounded-md bg-gray-900 text-white font-bold">+</button>
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
                if (!room) return alert("Mohon masukkan Nomor Kamar Anda.");

                let message = `*ORDER RESTORAN* 🔔\n`;
                message += `-----------------------------\n`;
                message += `Nama: *${USER_NAME}* (Akun Terdaftar)\n`; 
                message += `Email: ${USER_EMAIL}\n`;
                message += `Kamar: *${room}*\n`;
                message += `-----------------------------\n`;
                message += `*Pesanan:* \n`;

                let total = 0;
                cart.forEach(item => {
                    const subtotal = item.price * item.qty;
                    total += subtotal;
                    message += `▪️ ${item.name} (${item.qty}x)\n`;
                });

                message += `\n*TOTAL: Rp ${new Intl.NumberFormat('id-ID').format(total)}*`;
                message += `\n-----------------------------\n`;
                message += `_Mohon segera diproses._`;

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