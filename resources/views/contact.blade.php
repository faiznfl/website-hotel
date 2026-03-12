@extends('layouts.main')

@section('title', 'Contact Us - Hotel Rumah RB')

@section('content')

    <section class="py-12 md:py-20 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. HEADER TITLE --}}
            <div class="text-center mb-10 md:mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight uppercase">GET IN TOUCH</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500 text-sm md:text-base max-w-2xl mx-auto">
                    Hubungi kami untuk pertanyaan, reservasi, atau informasi lebih lanjut.
                </p>
            </div>

            {{-- 2. SECTION MAPS --}}
            <div class="relative w-full h-[300px] md:h-[450px] bg-gray-200 rounded-2xl overflow-hidden shadow-lg mb-12 md:mb-16 border border-gray-200 group">
                <div class="absolute inset-0 flex items-center justify-center bg-gray-100 -z-10">
                    <i class="fa-solid fa-map-location-dot text-4xl text-gray-300 animate-pulse"></i>
                </div>

                {{-- Ganti SRC di bawah dengan Link Embed Google Maps Hotel kamu --}}
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.288594248744!2d100.37568117431525!3d-0.9335606353406691!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4b90f47b2c55f%3A0x7d6f5f84d6738914!2sHotel%20Rumah%20RB!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" class="w-full h-full pointer-events-none transition-all duration-500 group-hover:scale-105">
                </iframe>

                <a href="https://maps.app.goo.gl/y3NnutUR4spzdZZYA" target="_blank"
                    class="absolute inset-0 z-10 flex items-end justify-center pb-6 group-hover:bg-black/5 transition-colors">
                    <span class="bg-white/90 backdrop-blur px-4 py-2 rounded-full text-xs font-bold shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        Buka di Google Maps <i class="fa-solid fa-arrow-up-right-from-square ml-1"></i>
                    </span>
                </a>
            </div>

            {{-- 3. GRID CONTENT --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-start">

                {{-- KOLOM KIRI: CONTACT FORM --}}
                <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-1 h-6 bg-yellow-400 rounded-full inline-block"></span> SEND A MESSAGE
                    </h3>

                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r text-sm flex items-center gap-3 animate-bounce">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                            <div>
                                <strong class="font-bold block">Berhasil Terkirim!</strong>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="text" name="fax_number" style="display:none !important" tabindex="-1" autocomplete="off">

                        <div>
                            <label for="nama" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                                class="w-full px-4 py-3 bg-gray-50 border @error('nama') border-red-500 @else border-gray-200 @enderror rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 text-sm"
                                placeholder="Enter your full name" required>
                            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full px-4 py-3 bg-gray-50 border @error('email') border-red-500 @else border-gray-200 @enderror rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 text-sm"
                                placeholder="name@example.com" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 bg-gray-50 border @error('phone') border-red-500 @else border-gray-200 @enderror rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 text-sm"
                                placeholder="+62..." required>
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="pesan" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Your Message</label>
                            <textarea id="pesan" name="pesan" rows="4"
                                class="w-full px-4 py-3 bg-gray-50 border @error('pesan') border-red-500 @else border-gray-200 @enderror rounded-lg focus:bg-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 transition-all duration-300 text-sm resize-none"
                                placeholder="Write your inquiries here..." required>{{ old('pesan') }}</textarea>
                            @error('pesan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-lg hover:bg-yellow-500 transition-all duration-300 shadow-lg hover:shadow-yellow-500/30 transform active:scale-95 uppercase tracking-wider text-sm flex justify-center items-center gap-2">
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
                        {{-- Address --}}
                        <div class="flex items-start gap-5 group">
                            <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300 shadow-sm flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Address</h4>
                                <p class="text-gray-700 text-sm leading-relaxed font-medium italic">
                                    Jl. Dr. Sutomo No. 4B Marapalam, Kec. Padang Tim., Kota Padang, Sumatera Barat 25125
                                </p>
                            </div>
                        </div>

                        {{-- WhatsApp --}}
                        <div class="flex items-start gap-5 group text-decoration-none">
                            <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300 shadow-sm flex-shrink-0">
                                <i class="fa-solid fa-phone text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Phone / WhatsApp</h4>
                                <a href="https://wa.me/6281363374155" target="_blank" class="text-gray-900 text-lg font-bold hover:text-yellow-600 transition underline decoration-yellow-400 underline-offset-4">
                                    +62 813-6337-4155
                                </a>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-start gap-5 group">
                            <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300 shadow-sm flex-shrink-0">
                                <i class="fa-solid fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email Inquiry</h4>
                                <a href="mailto:hotelrumahrb@gmail.com" class="text-gray-700 text-sm font-medium hover:text-yellow-600 transition">
                                    hotelrumahrb@gmail.com
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        const mapWrapper = document.querySelector('.group');
        const iframe = mapWrapper.querySelector('iframe');

        mapWrapper.addEventListener('click', () => {
            iframe.classList.remove('pointer-events-none');
        });

        mapWrapper.addEventListener('mouseleave', () => {
            iframe.classList.add('pointer-events-none');
        });
    </script>

@endsection