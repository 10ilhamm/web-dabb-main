@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cms/virtual_book_pages.css') }}">
@endpush

@section('breadcrumb_parent', 'CMS / ' . $feature->name)
@section('breadcrumb_active', 'Virtual Book')

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
                <h1 class="text-2xl font-bold text-gray-800">Virtual Book: {{ $feature->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola halaman buku virtual</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($feature->path)
            <a href="{{ url($feature->path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-800 border border-transparent text-white text-sm font-medium rounded-lg hover:bg-blue-900 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                Lihat Buku
            </a>
            @endif
            <a href="{{ route('cms.features.virtual_book_pages.create', $feature) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 border border-transparent text-white text-sm font-semibold rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Halaman
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm font-medium text-gray-600">Total Halaman</p>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-gray-800">{{ $feature->virtualBookPages->count() }}</h3>
                <p class="text-xs text-gray-500 mt-1">Halaman buku</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm font-medium text-gray-600">Sampul Depan</p>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-gray-800">{{ $feature->virtualBookPages->where('is_cover', true)->count() }}</h3>
                <p class="text-xs text-gray-500 mt-1">Cover book</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm font-medium text-gray-600">Sampul Belakang</p>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-gray-800">{{ $feature->virtualBookPages->where('is_back_cover', true)->count() }}</h3>
                <p class="text-xs text-gray-500 mt-1">Back cover</p>
            </div>
        </div>
    </div>

    <!-- Book Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Pengaturan Buku</h3>
        <form action="{{ route('cms.features.virtual_book_pages.settings', $feature) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampul Buku (Cover)</label>
                @if($feature->book_cover)
                <img src="{{ asset('storage/' . $feature->book_cover) }}" alt="Cover" class="w-[120px] h-[160px] object-cover rounded border border-gray-200 mb-2">
                <label class="flex items-center">
                    <input type="checkbox" name="remove_book_cover" value="1" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-500">Hapus</span>
                </label>
                @else
                <input type="file" name="book_cover" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Daftar</label>
                @if($feature->book_thumbnail)
                <img src="{{ asset('storage/' . $feature->book_thumbnail) }}" alt="Thumbnail" class="w-[120px] h-[160px] object-cover rounded border border-gray-200 mb-2">
                <label class="flex items-center">
                    <input type="checkbox" name="remove_book_thumbnail" value="1" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-500">Hapus</span>
                </label>
                @else
                <input type="file" name="book_thumbnail" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @endif
            </div>

            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Daftar Halaman Buku</h2>
        </div>
        <div>
            <table id="tableVirtualBookPages" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm font-medium border-b border-gray-100">
                        <th class="px-6 py-4 w-12">No</th>
                        <th class="px-6 py-4 w-28">Gambar</th>
                        <th class="px-6 py-4">Judul</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4 w-24">Urutan</th>
                        <th class="px-6 py-4 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($feature->virtualBookPages->sortBy('order') as $page)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            @if($page->image)
                            <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $page->title }}" class="w-16 h-12 object-cover rounded-md border border-gray-200 shadow-sm">
                            @else
                            <div class="w-16 h-12 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-xs text-gray-400">No Img</div>
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
                                <a href="{{ route('cms.features.virtual_book_pages.edit', [$feature, $page]) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('cms.features.virtual_book_pages.destroy', [$feature, $page]) }}" method="POST" onsubmit="return confirm('Yakin hapus halaman ini?');">
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
                            Belum ada halaman buku. Klik "Tambah Halaman" untuk memulai.
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
    $('#tableVirtualBookPages').DataTable({
        columnDefs: [{ orderable: false, targets: [0, 1, 5] }],
        order: [[4, 'asc']],
    });
});
</script>
@endpush
