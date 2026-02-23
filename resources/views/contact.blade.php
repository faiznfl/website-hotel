@extends('layouts.main')

@section('title', 'Contact Us - Hotel Rumah RB')

@section('content')

    {{-- WRAPPER UTAMA --}}
    <section class="py-12 md:py-20 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. HEADER TITLE (Tanpa Hero Image) --}}
            <div class="text-center mb-10 md:mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight uppercase">GET IN TOUCH</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500 text-sm md:text-base max-w-2xl mx-auto">
                    Hubungi kami untuk pertanyaan, reservasi, atau informasi lebih lanjut.
                </p>
            </div>

            {{-- 2. SECTION MAPS (LANGSUNG BERWARNA & KLIK BUKA APLIKASI) --}}
            <div
                class="relative w-full h-[300px] md:h-[450px] bg-gray-200 rounded-2xl overflow-hidden shadow-lg mb-12 md:mb-16 border border-gray-200 group">

                {{-- Loading Skeleton --}}
                <div class="absolute inset-0 flex items-center justify-center bg-gray-100 -z-10">
                    <i class="fa-solid fa-map-location-dot text-4xl text-gray-300 animate-pulse"></i>
                </div>

                {{--
                IFRAME MAPS (NORMAL)
                - Tidak ada class grayscale (langsung warna).
                - pointer-events-none: Supaya pas scroll halaman di HP, jari tidak "nyangkut" geser-geser map.
                --}}
                <iframe src="https://maps.google.com/maps?q=Hotel%20Rumah%20RB%20Padang&t=&z=15&ie=UTF8&iwloc=&output=embed"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" class="w-full h-full pointer-events-none">
                </iframe>

                {{--
                TOMBOL & LINK FULL COVER
                Klik dimanapun di area map, langsung buka Google Maps Asli.
                Ganti href="..." dengan Link Google Maps hotel kamu.
                --}}
                <a href="https://maps.google.com/?q=Hotel+Rumah+RB+Padang" target="_blank"
                    class="absolute inset-0 z-10 flex items-end justify-center pb-6 group-hover:bg-black/5 transition-colors">
                </a>
            </div>

            {{-- 3. GRID CONTENT (FORM & INFO) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-start">

                {{-- KOLOM KIRI: CONTACT FORM --}}
                <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-1 h-6 bg-yellow-400 rounded-full inline-block"></span> SEND A MESSAGE
                    </h3>

                    {{-- ALERT SUKSES --}}
                    @if(session('success'))
                        <div
                            class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r text-sm flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                            <div>
                                <strong class="font-bold block">Berhasil Terkirim!</strong>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Input Name --}}
                        <div>
                            <label for="nama"
                                class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Full
                                Name</label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('nama') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 placeholder-gray-300 text-sm"
                                placeholder="Enter your full name" required>
                            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $pesan }}</p> @enderror
                        </div>

                        {{-- Input Email --}}
                        <div>
                            <label for="email"
                                class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email
                                Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 placeholder-gray-300 text-sm"
                                placeholder="name@example.com" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $pesan }}</p> @enderror
                        </div>

                        {{-- Input Phone --}}
                        <div>
                            <label for="phone"
                                class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Phone
                                Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 placeholder-gray-300 text-sm"
                                placeholder="+62..." required>
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $pesan }}</p> @enderror
                        </div>

                        {{-- Input Message --}}
                        <div>
                            <label for="pesan"
                                class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Your
                                Message</label>
                            <textarea id="pesan" name="pesan" rows="4"
                                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('pesan') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 placeholder-gray-300 text-sm resize-none"
                                placeholder="Write your inquiries here..." required>{{ old('pesan') }}</textarea>
                            @error('pesan') <p class="text-red-500 text-xs mt-1">{{ $pesan }}</p> @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-lg hover:bg-yellow-500 transition-all duration-300 shadow-lg hover:shadow-yellow-500/30 transform active:scale-95 uppercase tracking-wider text-sm flex justify-center items-center gap-2">
                                Send Message <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- KOLOM KANAN: CONTACT INFO CARD --}}
                <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100 h-full">
                    <h3 class="text-xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                        <span class="w-1 h-6 bg-yellow-400 rounded-full inline-block"></span> CONTACT INFO
                    </h3>

                    <div class="space-y-8">

                        {{-- Item 1: Alamat --}}
                        <div class="flex items-start gap-5 group">
                            <div
                                class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300 shadow-sm flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Address</h4>
                                <p class="text-gray-700 text-sm leading-relaxed font-medium">
                                    Jl. Dr. Sutomo No. 4B Marapalam,<br>
                                    Kec. Padang Tim., Kota Padang,<br>
                                    Sumatera Barat 25125
                                </p>
                            </div>
                        </div>

                        {{-- Item 2: Phone --}}
                        <div class="flex items-start gap-5 group">
                            <div
                                class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300 shadow-sm flex-shrink-0">
                                <i class="fa-solid fa-phone text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Phone / WhatsApp
                                </h4>
                                <a href="https://wa.me/6281363374155" target="_blank"
                                    class="text-gray-900 text-lg font-bold hover:text-yellow-600 transition decoration-yellow-400 decoration-2 underline-offset-4">
                                    +62 813-6337-4155
                                </a>
                            </div>
                        </div>

                        {{-- Item 3: Email --}}
                        <div class="flex items-start gap-5 group">
                            <div
                                class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300 shadow-sm flex-shrink-0">
                                <i class="fa-solid fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email Inquiry</h4>
                                <a href="mailto:hotelrumahrb@gmail.com"
                                    class="text-gray-700 text-sm font-medium hover:text-yellow-600 transition break-all">
                                    hotelrumahrb@gmail.com
                                </a>
                            </div>
                        </div>

                        {{-- Social Media Mini (Opsional) --}}
                        <div class="pt-8 mt-4 border-t border-gray-100">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Follow Us</h4>
                            <div class="flex gap-4">
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-yellow-500 hover:text-white transition-all duration-300">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white transition-all duration-300">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection