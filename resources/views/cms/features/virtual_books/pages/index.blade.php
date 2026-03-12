@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cms/virtual_book_pages.css') }}">
@endpush

@section('breadcrumb_parent', 'CMS / ' . $feature->name)
@section('breadcrumb_active', 'Halaman Buku')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('cms.features.virtual_books.index', $feature) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm" style="background-color: #818284;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Halaman: {{ $book->title }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola halaman dalam buku ini</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('cms.features.virtual_books.edit', [$feature, $book]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Cover
            </a>
            <a href="{{ route('cms.features.virtual_books.pages.create', [$feature, $book]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 border border-transparent text-white text-sm font-semibold rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Halaman
            </a>
        </div>
    </div>

    <!-- Book Cover Preview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
        @if($book->thumbnail)
        <img src="{{ asset('storage/' . $book->thumbnail) }}" alt="Cover" class="w-20 h-28 object-cover rounded shadow">
        @elseif($book->cover_image)
        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover" class="w-20 h-28 object-cover rounded shadow">
        @else
        <div class="w-20 h-28 bg-gray-200 rounded flex items-center justify-center text-gray-400">No Cover</div>
        @endif
        <div>
            <h3 class="font-semibold text-gray-800">{{ $book->title }}</h3>
            <p class="text-sm text-gray-500">{{ $book->pages->count() }} halaman</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Daftar Halaman Buku</h2>
        </div>
        <div>
            <table id="tablePages" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm font-medium border-b border-gray-100">
                        <th class="px-6 py-4 w-12">No</th>
                        <th class="px-6 py-4 w-28">Thumbnail</th>
                        <th class="px-6 py-4">Judul</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4 w-24">Urutan</th>
                        <th class="px-6 py-4 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($book->pages->sortBy('order') as $page)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            @if($page->thumbnail)
                            <img src="{{ asset('storage/' . $page->thumbnail) }}" alt="{{ $page->title }}" class="w-16 h-20 object-cover rounded-md border border-gray-200 shadow-sm">
                            @else
                            <div class="w-16 h-20 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-xs text-gray-400">No Thumb</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-800">{{ $page->title ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($page->is_cover)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sampul Depan</span>
                            @elseif($page->is_back_cover)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sampul Belakang</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Halaman Isi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $page->order }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('cms.features.virtual_books.pages.edit', [$feature, $book, $page]) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('cms.features.virtual_books.pages.destroy', [$feature, $book, $page]) }}" method="POST" onsubmit="return confirm('Yakin hapus halaman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                            Belum ada halaman. Klik "Tambah Halaman" untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#tablePages').DataTable({
        columnDefs: [{ orderable: false, targets: [0, 1, 5] }],
        order: [[4, 'asc']],
    });
});
</script>
@endpush
