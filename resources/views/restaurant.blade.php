@extends('layouts.main')

@section('title', 'Menu Restoran - Hotel Rumah RB')

@section('content')

    {{-- 1. LIBRARY TOASTIFY (Untuk Notif Error Kecil di Atas) --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    {{-- ======================================================================= --}}
    {{-- 2. HERO SECTION --}}
    {{-- ======================================================================= --}}
    <div class="relative w-full h-[300px] md:h-[350px]">
        <img src="{{ asset('img/hotel-luar.png') }}" alt="Restoran Hotel" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-center px-4">
            <h1 class="text-3xl md:text-5xl font-bold text-white mb-2 shadow-sm">Restoran & Dining</h1>
            <div class="w-20 h-1 bg-yellow-500 rounded-full mb-4"></div>
            <p class="text-gray-200 text-sm md:text-base max-w-2xl">
                Nikmati hidangan lezat kami dari kenyamanan kamar Anda.
            </p>
        </div>
    </div>

    {{-- ======================================================================= --}}
    {{-- 3. MAIN CONTENT --}}
    {{-- ======================================================================= --}}
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- SEARCH & FILTER --}}
            <div class="bg-white rounded-lg shadow-sm p-4 mb-8 flex flex-col md:flex-row gap-4 justify-between items-center sticky top-0 z-20 border border-gray-100">
                <div class="flex overflow-x-auto no-scrollbar gap-2 w-full md:w-auto pb-1 md:pb-0">
                    <button onclick="filterMenu('all')" class="cat-btn active px-4 py-2 rounded-md text-sm font-medium transition-colors bg-gray-900 text-white shadow-sm">Semua</button>
                    <button onclick="filterMenu('makanan')" class="cat-btn px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">Makanan</button>
                    <button onclick="filterMenu('minuman')" class="cat-btn px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">Minuman</button>
                    <button onclick="filterMenu('snack')" class="cat-btn px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">Snack</button>
                </div>
                <div class="relative w-full md:w-72">
                    <input type="text" id="search-input" onkeyup="searchMenu()" placeholder="Cari menu..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                </div>
            </div>

            {{-- MENU GRID --}}
            @if($menus->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
                    @foreach($menus as $menu)
                        <div class="menu-item bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden flex flex-col h-full" data-category="{{ $menu->kategori }}">
                            <div class="relative h-48 bg-gray-200 overflow-hidden">
                                @if($menu->foto)
                                    <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-image text-3xl"></i></div>
                                @endif
                                <span class="absolute top-2 right-2 bg-white/90 px-2 py-1 text-xs font-bold rounded text-gray-700 shadow-sm uppercase">{{ $menu->kategori }}</span>
                            </div>
                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-1 leading-tight">{{ $menu->nama }}</h3>
                                <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $menu->deskripsi }}</p>
                                <div class="mt-auto flex items-center justify-between pt-3 border-t border-gray-50">
                                    <div class="text-lg font-bold text-yellow-600">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                                    @auth
                                        <button onclick="addToCart({{ $menu->id }}, '{{ $menu->nama }}', {{ $menu->harga }})" 
                                                class="add-btn bg-gray-900 text-white w-9 h-9 rounded flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-colors shadow-sm">
                                            <i class="fa-solid fa-plus transition-transform duration-300"></i>
                                        </button>
                                    @else
                                        <a href="/login" class="text-gray-400 hover:text-yellow-600"><i class="fa-solid fa-lock"></i></a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-lg border border-dashed border-gray-300"><p class="text-gray-500">Menu belum tersedia.</p></div>
            @endif
        </div>
    </div>

    {{-- ======================================================================= --}}
    {{-- 4. MODAL & POPUPS --}}
    {{-- ======================================================================= --}}
    @auth
        {{-- Floating Cart Button --}}
        <button onclick="toggleModal()" class="fixed bottom-6 right-6 z-40 bg-yellow-500 hover:bg-yellow-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-105 active:scale-95">
            <div class="relative">
                <i class="fa-solid fa-shopping-cart text-xl"></i>
                <span id="cart-badge" class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-yellow-500 hidden">0</span>
            </div>
        </button>

        {{-- KERANJANG MODAL --}}
        <div id="cart-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/50 transition-opacity" onclick="toggleModal()"></div>
            <div class="bg-white w-full max-w-md rounded-lg shadow-xl relative z-10 overflow-hidden flex flex-col max-h-[80vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-900">Keranjang Pesanan</h3>
                    <button onclick="toggleModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
                </div>
                <div class="flex-1 overflow-y-auto p-6 space-y-4" id="cart-items"></div>
                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    <div class="flex justify-between items-center mb-4 text-lg font-bold text-gray-900">
                        <span>Total:</span>
                        <span id="cart-total">Rp 0</span>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kamar</label>
                        <input type="text" id="room-number" placeholder="Contoh: 101" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-yellow-500">
                    </div>
                    <button onclick="showConfirm()" class="w-full bg-gray-900 text-white py-3 rounded-md font-bold hover:bg-gray-800 transition shadow-sm">
                        Konfirmasi Pesanan
                    </button>
                </div>
            </div>
        </div>

        {{-- 1. KONFIRMASI MODAL (Kecil) --}}
        <div id="confirm-modal" class="fixed inset-0 z-[60] hidden flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            <div class="bg-white w-full max-w-sm rounded-xl shadow-2xl relative z-10 p-6 text-center transform scale-100 transition-transform">
                <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-question text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Kirim Pesanan?</h3>
                <p class="text-gray-500 text-sm mb-5">Pesanan akan diteruskan ke dapur.</p>
                <div class="flex gap-3">
                    <button onclick="closeConfirm()" class="flex-1 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition text-sm">Batal</button>
                    <button onclick="finalProcess()" id="btn-final-send" class="flex-1 py-2.5 bg-gray-900 text-white font-bold rounded-lg hover:bg-black transition text-sm flex justify-center items-center gap-2">
                        <span>Ya, Kirim</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- 2. SUKSES POPUP (Kecil & Rapi) --}}
        <div id="success-popup" class="fixed inset-0 z-[70] hidden flex items-center justify-center px-4">
            {{-- Backdrop Ringan --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>
            
            {{-- Card Kecil --}}
            <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl relative z-10 p-6 text-center animate-bounce-in">
                <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm">
                    <i class="fa-solid fa-check text-2xl"></i>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-1">Berhasil!</h3>
                <p class="text-gray-500 text-sm mb-6">Pesanan Anda telah diterima.</p>
                <button onclick="closeSuccess()" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition shadow-lg">
                    Oke, Siap
                </button>
            </div>
        </div>
    @endauth

    {{-- ======================================================================= --}}
    {{-- 5. JAVASCRIPT --}}
    {{-- ======================================================================= --}}
    <script>
        function filterMenu(category) {
            document.querySelectorAll('.menu-item').forEach(item => item.style.display = (category === 'all' || item.dataset.category === category) ? 'flex' : 'none');
            document.querySelectorAll('.cat-btn').forEach(btn => {
                const isActive = btn.onclick.toString().includes(`'${category}'`);
                btn.className = isActive ? "cat-btn active px-4 py-2 rounded-md text-sm font-medium transition-colors bg-gray-900 text-white shadow-sm" : "cat-btn px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors";
            });
        }

        function searchMenu() {
            const val = document.getElementById('search-input').value.toLowerCase();
            document.querySelectorAll('.menu-item').forEach(item => item.style.display = item.querySelector('h3').innerText.toLowerCase().includes(val) ? 'flex' : 'none');
        }

        @auth
            const USER_EMAIL = "{{ auth()->user()->email }}";
            let cart = JSON.parse(localStorage.getItem('stdCart_' + USER_EMAIL)) || [];

            function saveCart() { localStorage.setItem('stdCart_' + USER_EMAIL, JSON.stringify(cart)); updateUI(); }

            function addToCart(id, name, price) {
                const item = cart.find(i => i.id === id);
                if(item) item.qty++; else cart.push({id, name, price, qty: 1});
                saveCart();
                // Button Effect
                const btn = event.currentTarget; const icon = btn.querySelector('i'); const oldClass = btn.className; const oldIcon = icon.className;
                btn.className = "bg-green-600 text-white w-9 h-9 rounded flex items-center justify-center transition-colors shadow-sm"; icon.className = "fa-solid fa-check";
                setTimeout(() => { btn.className = oldClass; icon.className = oldIcon; }, 800);
            }

            function updateUI() {
                const container = document.getElementById('cart-items'); const badge = document.getElementById('cart-badge'); const totalEl = document.getElementById('cart-total');
                const totalQty = cart.reduce((sum, i) => sum + i.qty, 0); const totalPrice = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
                badge.innerText = totalQty; badge.classList.toggle('hidden', totalQty <= 0); totalEl.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(totalPrice);
                
                if(cart.length === 0) container.innerHTML = `<div class="text-center py-4 text-gray-500">Keranjang kosong.</div>`;
                else container.innerHTML = cart.map(item => `
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0">
                        <div><h4 class="font-bold text-gray-900 text-sm">${item.name}</h4><p class="text-xs text-gray-500">Rp ${new Intl.NumberFormat('id-ID').format(item.price)} x ${item.qty}</p></div>
                        <div class="flex items-center gap-2"><button onclick="changeQty(${item.id}, -1)" class="w-6 h-6 bg-gray-200 rounded text-sm font-bold hover:bg-gray-300">-</button><span class="text-sm font-bold w-4 text-center">${item.qty}</span><button onclick="changeQty(${item.id}, 1)" class="w-6 h-6 bg-gray-200 rounded text-sm font-bold hover:bg-gray-300">+</button></div>
                    </div>`).join('');
            }

            function changeQty(id, delta) { const item = cart.find(i => i.id === id); if(item) { item.qty += delta; if(item.qty <= 0) cart = cart.filter(i => i.id !== id); saveCart(); } }
            function toggleModal() { document.getElementById('cart-modal').classList.toggle('hidden'); }
            
            // --- NOTIFIKASI ---
            function showToast(msg) {
                Toastify({ text: msg, duration: 3000, gravity: "top", position: "center", style: { background: "#1f2937", borderRadius: "8px", fontSize: "12px" } }).showToast();
            }

            function showConfirm() {
                const room = document.getElementById('room-number').value.trim();
                if(!room) { showToast("⚠️ Mohon isi Nomor Kamar!"); document.getElementById('room-number').focus(); return; }
                toggleModal();
                document.getElementById('confirm-modal').classList.remove('hidden');
            }

            function closeConfirm() {
                document.getElementById('confirm-modal').classList.add('hidden');
                toggleModal(); // Buka lagi keranjang
            }

            function closeSuccess() {
                document.getElementById('success-popup').classList.add('hidden');
                document.getElementById('room-number').value = '';
            }

            async function finalProcess() {
                const room = document.getElementById('room-number').value.trim();
                const btn = document.getElementById('btn-final-send');
                btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i>`; btn.disabled = true;

                try {
                    const response = await fetch("{{ route('restaurant.order.store') }}", {
                        method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify({ room_number: room, cart: cart })
                    });
                    
                    if(response.ok) {
                        cart = []; saveCart();
                        document.getElementById('confirm-modal').classList.add('hidden');
                        document.getElementById('success-popup').classList.remove('hidden'); // Tampilkan Popup Kecil
                    } else {
                        showToast("❌ Gagal mengirim pesanan.");
                        btn.innerHTML = `<span>Ya, Kirim</span>`; btn.disabled = false;
                    }
                } catch(e) {
                    showToast("❌ Error koneksi internet.");
                    btn.innerHTML = `<span>Ya, Kirim</span>`; btn.disabled = false;
                }
            }

            document.addEventListener('DOMContentLoaded', () => { updateUI(); filterMenu('all'); });
        @endauth
        document.addEventListener('DOMContentLoaded', () => { if(typeof updateUI === 'undefined') filterMenu('all'); });
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); }
        }
        .animate-bounce-in { animation: bounce-in 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    </style>

@endsection