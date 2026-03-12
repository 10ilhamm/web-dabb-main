@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cms/virtual_book_pages.css') }}">
@endpush

@section('breadcrumb_parent', 'CMS / ' . $feature->name)
@section('breadcrumb_active', 'Daftar Buku')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ $feature->parent ? route('cms.features.show', $feature->parent) : route('cms.features.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm" style="background-color: #818284;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Buku: {{ $feature->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola buku dalam fitur ini</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('cms.features.virtual_books.create', $feature) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 border border-transparent text-white text-sm font-semibold rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Buku
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Daftar Buku</h2>
        </div>
        <div>
            <table id="tableBooks" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm font-medium border-b border-gray-100">
                        <th class="px-6 py-4 w-12">No</th>
                        <th class="px-6 py-4 w-28">Cover</th>
                        <th class="px-6 py-4">Judul Buku</th>
                        <th class="px-6 py-4 w-24">Jml Halaman</th>
                        <th class="px-6 py-4 w-24">Urutan</th>
                        <th class="px-6 py-4 w-48 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $book)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            @if($book->thumbnail)
                            <img src="{{ asset('storage/' . $book->thumbnail) }}" alt="{{ $book->title }}" class="w-16 h-20 object-cover rounded-md border border-gray-200 shadow-sm">
                            @elseif($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-16 h-20 object-cover rounded-md border border-gray-200 shadow-sm">
                            @else
                            <div class="w-16 h-20 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-xs text-gray-400">No Cover</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-800">{{ $book->title }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $book->pages->count() }} halaman</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $book->order }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Detail - ke daftar halaman -->
                                <a href="{{ route('cms.features.virtual_books.pages.index', [$feature, $book]) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors"
                                    title="Detail - Kelola Halaman">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </a>
                                <!-- Edit - edit cover buku -->
                                <a href="{{ route('cms.features.virtual_books.edit', [$feature, $book]) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors"
                                    title="Edit Cover Buku">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <!-- Delete -->
                                <form action="{{ route('cms.features.virtual_books.destroy', [$feature, $book]) }}" method="POST" onsubmit="return confirm('Yakin hapus buku ini? Semua halaman juga akan dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors" title="Hapus Buku">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                            Belum ada buku. Klik "Tambah Buku" untuk membuat buku pertama.
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
    $('#tableBooks').DataTable({
        columnDefs: [{ orderable: false, targets: [0, 1, 5] }],
        order: [[4, 'asc']],
    });
});
</script>
@endpush
