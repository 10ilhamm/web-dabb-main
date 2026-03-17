@extends('layouts.app')

@section('breadcrumb_parent', 'CMS / ' . __('cms.features.title') . ' / <a href="' . route('cms.features.slideshow.index', $feature) . '">' . __('cms.virtual_slideshow.title') . '</a>')
@section('breadcrumb_active', 'Tambah Halaman')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('cms.features.slideshow.index', $feature) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm" style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Halaman Exhibition</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
        </div>
    </div>

    <form action="{{ route('cms.features.slideshow.pages.store', $feature) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-800">Informasi Halaman</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Halaman <span class="text-red-500">*</span></label>
                <input type="text" name="title" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Judul halaman exhibition..." required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Deskripsi singkat..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutan <span class="text-red-500">*</span></label>
                <input type="number" name="order" min="0" value="1" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <p class="text-xs text-gray-500 mt-1">Urutan tampilan di halaman publik</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('cms.features.slideshow.index', $feature) }}"
                class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors shadow-sm">
                Simpan Halaman
            </button>
        </div>
    </form>
</div>
@endsection