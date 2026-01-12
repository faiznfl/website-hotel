@extends('layouts.main')

@section('title', 'Contact Us - Hotel Rumah RB')

@section('content')

    <section class="py-16 bg-white min-h-screen">
        <div class="max-w-screen-xl mx-auto px-4">

            {{-- 1. HEADER TITLE (Agar konsisten dengan halaman lain) --}}
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight uppercase">GET IN TOUCH</h2>
                <div class="w-20 h-1 bg-yellow-500 mx-auto mt-4 rounded-full"></div>
            </div>

            {{-- 2. SECTION MAPS --}}
            <div class="w-full h-[400px] bg-gray-100 rounded-2xl overflow-hidden shadow-lg mb-16 border border-gray-200">
                <iframe src="https://maps.google.com/maps?q=Hotel%20Rumah%20RB%20Padang&t=&z=15&ie=UTF8&iwloc=&output=embed"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" class="grayscale hover:grayscale-0 transition-all duration-500">
                </iframe>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start">

                {{-- 3. KOLOM KIRI: CONTACT FORM --}}
<div class="bg-white p-0 lg:p-4">
    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
        <span class="w-8 h-[2px] bg-yellow-500 inline-block"></span> SEND A MESSAGE
    </h3>

    {{-- TAMPILKAN PESAN SUKSES DI SINI --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative text-sm" role="alert">
            <strong class="font-bold"><i class="fa-solid fa-check-circle"></i> Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Arahkan action ke route 'contact.send' --}}
    <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Input Name --}}
        <div>
            <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                Full Name
            </label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all duration-300 placeholder-gray-300"
                placeholder="Enter your full name" required>
            
            {{-- Pesan Error Validasi --}}
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Input Email --}}
        <div>
            <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                Email Address
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all duration-300 placeholder-gray-300"
                placeholder="name@example.com" required>

            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Input Phone (Sesuai update sebelumnya) --}}
        <div>
            <label for="phone" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                Phone Number
            </label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all duration-300 placeholder-gray-300"
                placeholder="+62..." required>

            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Input Message --}}
        <div>
            <label for="message" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                Your Message
            </label>
            <textarea id="message" name="message" rows="5"
                class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('message') ? 'border-red-500' : 'border-gray-200' }} rounded-lg focus:bg-white focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all duration-300 placeholder-gray-300"
                placeholder="Write your inquiries here..." required>{{ old('message') }}</textarea>

            @error('message')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit"
                class="w-full md:w-auto px-10 py-3.5 bg-gray-900 text-white font-bold uppercase tracking-wider rounded-lg hover:bg-yellow-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                Send Message
            </button>
        </div>
    </form>
</div>

                {{-- 4. KOLOM KANAN: CONTACT INFO --}}
                <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                        <span class="w-8 h-[2px] bg-yellow-500 inline-block"></span> CONTACT INFO
                    </h3>

                    <div class="space-y-8">

                        {{-- Alamat --}}
                        <div class="flex items-start group">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-location-dot text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-1">Address</h4>
                                <p class="text-gray-600 leading-relaxed">
                                    Jl. Dr. Sutomo No. 4B Marapalam<br>
                                    Padang, Indonesia 25125
                                </p>
                            </div>
                        </div>

                        {{-- Telepon --}}
                        <div class="flex items-start group">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-phone text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-1">Phone</h4>
                                <p class="text-gray-600 font-medium hover:text-yellow-600 transition-colors cursor-pointer">
                                    +62 813-6337-4155
                                </p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-start group">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-envelope text-xl"></i>
                            </div>
                            <div class="ml-5">
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-1">Email</h4>
                                <p class="text-gray-600 font-medium hover:text-yellow-600 transition-colors cursor-pointer">
                                    hotelrumahrb@gmail.com
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection