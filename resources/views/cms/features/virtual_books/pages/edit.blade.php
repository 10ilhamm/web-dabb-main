@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cms/virtual_book_pages.css') }}">
<link rel="stylesheet" href="{{ asset('css/cms/virtual_books/pages/page-editor.css') }}">
@endpush

@section('breadcrumb_parent', __('cms.virtual_book_pages.breadcrumb_parent'))
@section('breadcrumb_active', __('cms.virtual_book_pages.breadcrumb_edit'))

@section('content')
<div class="mb-4">
    <a href="{{ route('cms.features.virtual_books.pages.index', [$feature, $book]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm" style="background-color: #818284;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali ke Daftar
    </a>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Halaman: {{ $virtualBookPage->title ?: 'Halaman ' . $virtualBookPage->order }}</h1>
    <p class="text-sm text-gray-500 mt-1">Perbarui informasi halaman buku virtual</p>
</div>

<form action="{{ route('cms.features.virtual_books.pages.update', [$feature, $book, $virtualBookPage]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="flex gap-6 items-start" style="flex-wrap: nowrap;">

        <!-- Left Column: Form Fields -->
        <div class="space-y-6" style="width: 38%; min-width: 380px; flex-shrink: 0;">

            <!-- Multiple Image Upload Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Gambar Halaman</h3>

                <div class="space-y-4">
                    <!-- Existing Images -->
                    @php
                        $existingImages = $virtualBookPage->page_images ?? [];
                        $imagePositionsData = $virtualBookPage->image_positions ?? [];
                    @endphp

                    @if(count($existingImages) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                        <div class="image-thumbnails" id="existingThumbnails">
                            @foreach($existingImages as $index => $image)
                            <div class="image-thumbnail">
                                <img src="{{ asset('storage/' . $image) }}" alt="Image {{ $index + 1 }}">
                                <button type="button" class="remove-btn" onclick="removeExistingImage({{ $index }})">&times;</button>
                                <span class="existing-label">Ada</span>
                                <div class="position-label">Img {{ $index + 1 }}</div>
                            </div>
                            @endforeach
                        </div>
                        <label class="flex items-center mt-2">
                            <input type="checkbox" name="remove_all_images" value="1" class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-500">Hapus semua gambar</span>
                        </label>
                    </div>
                    <hr class="border-gray-200">
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar Baru</label>
                        <input type="file" name="images[]" accept="image/*" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" onchange="handleImageUpload(this)">
                        <p class="text-xs text-gray-500 mt-1.5">JPG, PNG, atau WebP. Maks 2MB per gambar.</p>
                    </div>

                    <!-- New Image Thumbnails -->
                    <div id="imageThumbnails" class="image-thumbnails"></div>
                </div>
            </div>

            <!-- Page Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Informasi Halaman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman</label>
                        <input type="text" name="title" id="pageTitleInput" value="{{ old('title', $virtualBookPage->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Masukkan judul halaman" oninput="updatePreview()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten Teks</label>
                        <textarea name="content" id="pageContentInput" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Masukkan konten teks halaman" oninput="updatePreview()">{{ old('content', $virtualBookPage->content) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran Gambar (%)</label>
                        <div class="flex items-center gap-3">
                            <input type="range" name="image_height" id="imageHeightSlider" min="10" max="100" value="{{ old('image_height', $virtualBookPage->image_height ?? 50) }}" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('imageHeightValue').textContent = this.value + '%'; updatePreview()">
                            <span id="imageHeightValue" class="text-sm text-gray-600 w-12 text-right">{{ old('image_height', $virtualBookPage->image_height ?? 50) }}%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Atur tinggi gambar dalam halaman</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan <span class="text-red-500">*</span></label>
                        <input type="number" name="order" value="{{ old('order', $virtualBookPage->order) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <p class="text-xs text-gray-500 mt-1">Urutan tampilan halaman dalam buku</p>
                    </div>
                </div>
            </div>

            <!-- Thumbnail Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Thumbnail Halaman</h3>

                <div class="space-y-4">
                    @if($virtualBookPage->thumbnail)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Saat Ini</label>
                        <img src="{{ asset('storage/' . $virtualBookPage->thumbnail) }}" alt="Thumbnail" class="w-24 h-32 object-cover rounded-lg border border-gray-200">
                        <label class="flex items-center mt-2">
                            <input type="checkbox" name="remove_thumbnail" value="1" class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-500">Hapus thumbnail</span>
                        </label>
                        <hr class="my-3 border-gray-200">
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Thumbnail Baru</label>
                        <input type="file" name="thumbnail" id="thumbnailInput" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <input type="hidden" name="generated_thumbnail" id="generatedThumbnail">

                        <!-- Thumbnail Preview -->
                        <div id="thumbnailPreviewContainer" class="mt-2 hidden">
                            <p class="text-xs text-gray-500 mb-1">Thumbnail baru yang akan disimpan:</p>
                            <img id="thumbnailPreview" class="w-24 h-32 object-cover rounded-lg border border-gray-200" alt="Thumbnail Preview">
                            <button type="button" id="removeThumbnail" class="mt-1 text-xs text-red-500 hover:text-red-700">Batal</button>
                        </div>

                        <div class="flex items-center gap-2 mt-2">
                            <button type="button" id="generateThumbnailBtn" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-md hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Generate dari Preview
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">Atau upload manual. Generate akan membuat thumbnail dari preview halaman.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('cms.features.virtual_books.pages.index', [$feature, $book]) }}" class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm" style="background-color:#1d4ed8;">
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- Right Column: Live Preview -->
        <div class="flex-1" style="width: 62%;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-1">Preview Halaman</h3>
                <p class="text-xs text-gray-500 mb-2">Geser langsung elemen di preview dengan cursor</p>

                <div class="book-preview-container">
                    <div class="book-preview-wrapper">
                        <div id="bookPreview" class="book-preview content-page" data-page-order="{{ $virtualBookPage->order }}">
                            <!-- Preview content will be rendered by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for positions -->
                <div id="positionInputs"></div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@php
    $textPos = $virtualBookPage->text_position ?? ['x' => 0, 'y' => 0, 'width' => 45, 'height' => 30];
@endphp
<script>
    window.pageEditorConfig = {
        existingImages: @json($existingImages),
        existingImagePositions: @json($imagePositionsData),
        textPosition: @json($textPos)
    };
</script>
<script src="{{ asset('js/cms/features/virtual_books/pages/page-edit.js') }}"></script>
@endpush
