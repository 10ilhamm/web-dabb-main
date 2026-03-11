@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/cms/virtual_book_pages.css') }}">
<style>
    /* Book Preview Styles */
    .book-preview-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        min-height: 500px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .book-preview-wrapper {
        perspective: 1500px;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .book-preview {
        width: 280px;
        height: 380px;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.5s ease;
    }

    /* Hard Cover Style */
    .book-preview.hard-cover {
        background: linear-gradient(135deg, #8B4513 0%, #654321 100%);
        border-radius: 4px 12px 12px 4px;
        box-shadow:
            -5px 5px 20px rgba(0,0,0,0.4),
            inset -2px 0 10px rgba(0,0,0,0.2);
    }

    /* Cover Front */
    .book-preview.cover-front {
        background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
        border-radius: 4px 12px 12px 4px;
        box-shadow:
            -8px 8px 25px rgba(0,0,0,0.5),
            inset -3px 0 15px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 30px;
    }

    .book-preview.cover-front .cover-title {
        color: #d4af37;
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .book-preview.cover-front .cover-image {
        width: 100%;
        height: auto;
        max-height: 200px;
        object-fit: contain;
        margin-top: 20px;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    /* Back Cover */
    .book-preview.cover-back {
        background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
        border-radius: 12px 4px 4px 12px;
        box-shadow:
            8px 8px 25px rgba(0,0,0,0.5),
            inset 3px 0 15px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 30px;
    }

    .book-preview.cover-back .cover-title {
        color: #d4af37;
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .book-preview.cover-back .cover-image {
        width: 100%;
        height: auto;
        max-height: 200px;
        object-fit: contain;
        margin-top: 20px;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    /* Content Page (Book Look) */
    .book-preview.content-page {
        background: linear-gradient(135deg, #f5f5dc 0%, #faf8ef 100%);
        border-radius: 2px 10px 10px 2px;
        box-shadow:
            -3px 3px 15px rgba(0,0,0,0.3),
            inset -5px 0 20px -5px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Book Spine */
    .book-preview.content-page::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 20px;
        background: linear-gradient(90deg, #8B7355 0%, #a08060 50%, #c4a77d 100%);
        border-radius: 2px 0 0 2px;
    }

    .content-page-inner {
        flex: 1;
        padding: 25px 20px 20px 35px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .content-page-header {
        font-size: 0.75rem;
        text-transform: uppercase;
        text-align: center;
        color: #5a4a3a;
        padding-bottom: 8px;
        border-bottom: 1px solid #d4c4a8;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .content-page-image {
        width: 100%;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 4px;
        margin-bottom: 10px;
        min-height: 80px;
    }

    .content-page-text {
        flex: 1;
        font-size: 0.65rem;
        text-align: justify;
        color: #3a2a1a;
        line-height: 1.4;
        overflow-y: auto;
        padding: 8px;
        background: rgba(255,255,255,0.3);
        border-radius: 4px;
    }

    .content-page-footer {
        font-size: 0.6rem;
        text-align: center;
        color: #8a7a6a;
        padding-top: 8px;
        border-top: 1px solid #d4c4a8;
        margin-top: auto;
    }

    /* Position controls */
    .position-controls {
        display: none;
        margin-top: 10px;
        padding: 10px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .position-controls.show {
        display: block;
    }

    .position-controls label {
        display: block;
        font-size: 0.7rem;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .position-controls input[type="range"] {
        width: 100%;
        margin-bottom: 8px;
    }
</style>

@section('breadcrumb_parent', __('cms.virtual_book_pages.breadcrumb_parent'))
@section('breadcrumb_active', __('cms.virtual_book_pages.breadcrumb_create'))

@section('content')
<div class="mb-4">
    <a href="{{ route('cms.features.virtual_book_pages.index', $feature) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm" style="background-color: #818284;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali ke Daftar
    </a>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Halaman Buku</h1>
    <p class="text-sm text-gray-500 mt-1">Tambahkan halaman baru untuk buku virtual</p>
</div>

<form action="{{ route('cms.features.virtual_book_pages.store', $feature) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="flex gap-6 items-start" style="flex-wrap: nowrap;">

        <!-- Left Column: Form Fields -->
        <div class="space-y-6" style="width: 38%; min-width: 380px; flex-shrink: 0;">

            <!-- Image Upload Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Gambar Halaman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar</label>
                        <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" onchange="handleImageUpload(this)">
                        <p class="text-xs text-gray-500 mt-1.5">JPG, PNG, atau WebP. Maks 2MB.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran Gambar (%)</label>
                        <div class="flex items-center gap-3">
                            <input type="range" name="image_height" id="imageHeightSlider" min="10" max="100" value="50" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('imageHeightValue').textContent = this.value + '%'; updatePreview()">
                            <span id="imageHeightValue" class="text-sm text-gray-600 w-12 text-right">50%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Atur tinggi gambar dalam halaman</p>
                    </div>
                </div>
            </div>

            <!-- Page Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Informasi Halaman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Halaman <span class="text-red-500">*</span></label>
                        <select name="page_type" id="pageTypeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="updatePreview()">
                            <option value="content">Halaman Isi</option>
                            <option value="cover">Sampul Depan</option>
                            <option value="back_cover">Sampul Belakang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman</label>
                        <input type="text" name="title" id="pageTitleInput" value="{{ old('title') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Masukkan judul halaman" oninput="updatePreview()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten Teks</label>
                        <textarea name="content" id="pageContentInput" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Masukkan konten teks halaman" oninput="updatePreview()">{{ old('content') }}</textarea>
                    </div>

                    <!-- Position Controls for Content Page -->
                    <div id="contentPositionControls" class="position-controls">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Posisi Elemen</label>

                        <label>Posisi Gambar (%)</label>
                        <input type="range" id="imageYPosition" min="0" max="100" value="0" oninput="updatePreview()">

                        <label>Posisi Teks (%)</label>
                        <input type="range" id="contentYPosition" min="0" max="100" value="50" oninput="updatePreview()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan <span class="text-red-500">*</span></label>
                        <input type="number" name="order" value="{{ $maxOrder + 1 }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <p class="text-xs text-gray-500 mt-1">Urutan tampilan halaman dalam buku</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('cms.features.virtual_book_pages.index', $feature) }}" class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm" style="background-color:#1d4ed8;">
                    Simpan Halaman
                </button>
            </div>
        </div>

        <!-- Right Column: Live Preview -->
        <div class="flex-1" style="width: 62%;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-1">Preview Halaman</h3>
                <p class="text-xs text-gray-500 mb-4">Tampilan live halaman buku</p>

                <div class="book-preview-container">
                    <div class="book-preview-wrapper">
                        <div id="bookPreview" class="book-preview content-page">
                            <!-- Preview content will be rendered by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let newImageSrc = '';

    function handleImageUpload(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                newImageSrc = e.target.result;
                updatePreview();
            };
            reader.readAsDataURL(file);
        } else {
            newImageSrc = '';
            updatePreview();
        }
    }

    function getImageSrc() {
        return newImageSrc;
    }

    function updatePreview() {
        const pageType = document.getElementById('pageTypeSelect').value;
        const title = document.getElementById('pageTitleInput').value || 'Judul Halaman';
        const content = document.getElementById('pageContentInput').value;
        const imageHeight = document.getElementById('imageHeightSlider').value;
        const imageSrc = getImageSrc();
        const hasImage = imageSrc !== '';

        const preview = document.getElementById('bookPreview');
        const positionControls = document.getElementById('contentPositionControls');

        // Clear preview
        preview.className = 'book-preview';
        preview.innerHTML = '';

        if (pageType === 'cover') {
            // Front Cover
            preview.classList.add('cover-front');

            let coverContent = `<div class="cover-title">${title}</div>`;
            if (hasImage) {
                coverContent += `<img src="${imageSrc}" class="cover-image" alt="Cover">`;
            }
            preview.innerHTML = coverContent;

            positionControls.classList.remove('show');

        } else if (pageType === 'back_cover') {
            // Back Cover
            preview.classList.add('cover-back');

            let coverContent = `<div class="cover-title">${title || 'THE END'}</div>`;
            if (hasImage) {
                coverContent += `<img src="${imageSrc}" class="cover-image" alt="Cover">`;
            }
            preview.innerHTML = coverContent;

            positionControls.classList.remove('show');

        } else {
            // Content Page
            preview.classList.add('content-page');
            positionControls.classList.add('show');

            let innerContent = '<div class="content-page-inner">';

            // Header (title)
            if (title) {
                innerContent += `<div class="content-page-header">${title}</div>`;
            }

            // Image
            if (hasImage) {
                innerContent += `<div class="content-page-image" style="background-image: url('${imageSrc}'); height: ${imageHeight}%;"></div>`;
            }

            // Content text
            if (content) {
                innerContent += `<div class="content-page-text">${content.replace(/\n/g, '<br>')}</div>`;
            }

            // Footer
            innerContent += `<div class="content-page-footer">{{ $maxOrder + 1 }}</div>`;

            innerContent += '</div>';
            preview.innerHTML = innerContent;
        }
    }

    // Initial render
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();
    });
</script>
@endpush
