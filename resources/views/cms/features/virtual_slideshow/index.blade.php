@extends('layouts.app')

@section('breadcrumb_parent', 'CMS / ' . __('cms.features.title'))
@section('breadcrumb_active', $feature->name . ' — Slideshow')

@section('content')
<div class="space-y-6" x-data="{ deleteModal: { open: false, id: null, name: '' } }">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        @php
            $backRoute = $feature->parent_id ? route('cms.features.show', $feature->parent_id) : route('cms.features.index');
        @endphp
        <a href="{{ $backRoute }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm" style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🎞️ Pameran Arsip Virtual SlideShow</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl text-sm flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Pages List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Daftar Halaman / Exhibition</h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola halaman pameran arsip virtual dan konten slide di dalamnya.</p>
            </div>
            <a href="{{ route('cms.features.slideshow.pages.create', $feature) }}"
                class="flex items-center gap-2 bg-[#174E93] hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Tambah Halaman
            </a>
        </div>

        @if($pages->isEmpty())
        <div class="px-6 py-20 text-center">
            <div class="flex flex-col items-center gap-4">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-sm">Belum ada halaman. Buat halaman terlebih dahulu di menu "Kelola Halaman".</p>
                <a href="{{ route('cms.features.slideshow.pages.create', $feature) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#174E93] text-white text-sm font-semibold rounded-lg hover:bg-blue-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Halaman
                </a>
            </div>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($pages as $page)
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/60 transition-colors">
                <div class="text-gray-400 font-medium text-sm w-6 text-center">{{ $page->order }}</div>

                {{-- Thumbnail --}}
                <div class="w-20 h-14 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 flex items-center justify-center">
                    @if($page->thumbnail_path)
                        <img src="{{ asset('storage/' . $page->thumbnail_path) }}" class="w-full h-full object-cover" alt="">
                    @elseif($page->slideshowSlides && $page->slideshowSlides->count() > 0)
                        @php
                            $firstSlide = $page->slideshowSlides->first();
                        @endphp
                        @if($firstSlide->slide_type === 'hero')
                            <div class="w-full h-full bg-gradient-to-br from-[#174E93] to-blue-400 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                                </svg>
                            </div>
                        @elseif($firstSlide->images && count($firstSlide->images) > 0)
                            <img src="{{ asset('storage/' . $firstSlide->images[0]) }}" class="w-full h-full object-cover" alt="">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">
                        {{ app()->getLocale() === 'en' && $page->title_en ? $page->title_en : $page->title }}
                    </p>
                    @if($page->description)
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ Str::limit($page->description, 80) }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                        <span>📄 {{ $page->slideshowSlides_count ?? 0 }} slides</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('cms.features.slideshow.pages.slides.index', [$feature, $page]) }}"
                        class="inline-flex items-center justify-center w-8 h-8 bg-[#174E93] hover:bg-blue-800 text-white rounded-md transition-colors" title="Kelola Slides">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </a>
                    <a href="{{ route('cms.features.slideshow.pages.edit', [$feature, $page]) }}"
                        class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors" title="Edit Halaman">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <button type="button"
                        @click="deleteModal = { open: true, id: {{ $page->id }}, name: '{{ addslashes($page->title) }}' }"
                        class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors" title="Hapus">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
            <a href="{{ url($feature->path ?? '#') }}" target="_blank"
                class="inline-flex items-center gap-2 text-sm text-[#174E93] hover:underline font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Halaman Publik
            </a>
        </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Hapus Halaman</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus halaman
                        <strong x-text="deleteModal.name" class="text-gray-700"></strong>?
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full">
                    <button @click="deleteModal.open = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <form :action="`{{ url('cms/features/' . $feature->id . '/slideshow/pages') }}/${deleteModal.id}`" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
