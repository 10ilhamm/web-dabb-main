@extends('layouts.app')

@section('breadcrumb_parent', __('cms.virtual_book_pages.breadcrumb_parent'))
@section('breadcrumb_active', 'Tambah Buku')

@section('content')
<div class="mb-4">
    <a href="{{ route('cms.features.virtual_books.index', $feature) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm" style="background-color: #818284;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali ke Daftar Buku
    </a>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Buku Baru</h1>
    <p class="text-sm text-gray-500 mt-1">Buat buku baru dalam fitur {{ $feature->name }}</p>
</div>

<form action="{{ route('cms.features.virtual_books.store', $feature) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="bookForm">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="bookTitle" value="{{ old('title') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Masukkan judul buku" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                    <input type="file" name="cover_image" id="coverImageInput" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1.5">JPG, PNG, atau WebP.</p>
                </div>

                <!-- Additional Texts Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teks Tambahan (Opsional)</label>
                    <p class="text-xs text-gray-500 mb-2">Tambahkan teks seperti subjudul atau deskripsi sampul</p>

                    <div id="additionalTextsContainer" class="space-y-2">
                        <!-- Dynamic text fields will be added here -->
                    </div>

                    <button type="button" id="addTextBtn" class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Teks
                    </button>
                </div>

                <!-- Back Cover Section -->
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Sampul Belakang</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku (Belakang)</label>
                    <input type="text" name="back_title" id="backBookTitle" value="{{ old('back_title') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Judul untuk sampul belakang (opsional)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku (Belakang)</label>
                    <input type="file" name="back_cover_image" id="backCoverImageInput" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1.5">JPG, PNG, atau WebP. Opsional.</p>
                </div>

                <!-- Back Cover Additional Texts Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teks Tambahan (Belakang)</label>
                    <p class="text-xs text-gray-500 mb-2">Tambahkan teks untuk sampul belakang</p>

                    <div id="backAdditionalTextsContainer" class="space-y-2">
                        <!-- Dynamic text fields will be added here -->
                    </div>

                    <button type="button" id="addBackTextBtn" class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Teks
                    </button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Daftar</label>
                    <input type="file" name="thumbnail" id="thumbnailInput" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <input type="hidden" name="generated_thumbnail" id="generatedThumbnail">

                    <!-- Thumbnail Preview -->
                    <div id="thumbnailPreviewContainer" class="mt-2 hidden">
                        <p class="text-xs text-gray-500 mb-1">Thumbnail yang akan disimpan:</p>
                        <img id="thumbnailPreview" class="w-24 h-32 object-cover rounded-lg border border-gray-200" alt="Thumbnail Preview">
                        <button type="button" id="removeThumbnail" class="mt-1 text-xs text-red-500 hover:text-red-700">Hapus</button>
                    </div>

                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" id="generateThumbnailBtn" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-md hover:bg-green-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Generate dari Preview
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5">Atau upload manual. Generate akan membuat thumbnail dari preview buku.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan <span class="text-red-500">*</span></label>
                    <input type="number" name="order" value="{{ $maxOrder + 1 }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <p class="text-xs text-gray-500 mt-1">Urutan tampilan buku dalam fitur</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('cms.features.virtual_books.index', $feature) }}" class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm" style="background-color:#1d4ed8;">
                    Simpan Buku
                </button>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Preview Cover Buku</h3>

            <div class="relative flex justify-center">
                <!-- Book Container -->
                <div id="bookPreview" class="relative w-48 h-64 bg-gradient-to-b from-amber-700 to-amber-900 rounded-r-md shadow-lg overflow-hidden" style="box-shadow: 4px 4px 15px rgba(0,0,0,0.3);">
                    <!-- Spine -->
                    <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-amber-900 to-amber-700"></div>

                    <!-- Cover Image Container - Draggable & Resizable -->
                    <div id="coverContainer" class="absolute inset-3 left-6 cursor-move flex items-center justify-center bg-white/10">
                        <span id="coverPlaceholder" class="text-white/50 text-xs text-center px-4">
                            Upload cover untuk preview
                        </span>
                        <img id="coverPreview" class="max-w-full max-h-full object-contain pointer-events-none" style="display: none;">
                        <!-- Resize Border - appears when image is uploaded -->
                        <div id="resizeBorder" class="absolute inset-0 border-2 border-dashed border-gray-400/50 opacity-0 transition-opacity pointer-events-none" style="display: none;"></div>
                    </div>

                    <!-- Draggable Title -->
                    <div id="titleContainer" class="absolute top-4 left-0 right-0 text-center px-4 cursor-move select-none">
                        <span id="previewTitle" class="text-white text-xs font-semibold drop-shadow-md line-clamp-2">
                            Judul Buku
                        </span>
                    </div>

                    <!-- Additional Texts Container - Draggable -->
                    <div id="additionalTextsPreview" class="absolute left-0 right-0 text-center px-4 cursor-move" style="bottom: 16px;">
                        <!-- Dynamic text previews will be added here -->
                    </div>
                </div>
            </div>

            <!-- Position Controls -->
            <div class="mt-4 space-y-3">
                <!-- Zoom Controls -->
                <div class="flex items-center justify-center gap-2">
                    <button type="button" id="zoomOutBtn" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors" title="Perkecil">−</button>
                    <input type="range" id="zoomSlider" min="30" max="250" value="100" class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    <button type="button" id="zoomInBtn" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors" title="Perbesar">+</button>
                    <span id="zoomLevel" class="text-xs text-gray-500 ml-2 w-12">100%</span>
                </div>
                <div class="flex items-center justify-center gap-4">
                    <button type="button" id="resetPosition" class="text-xs text-gray-500 hover:text-gray-700 underline">
                        Reset Posisi
                    </button>
                    <span class="text-xs text-gray-400">|</span>
                    <span class="text-xs text-gray-500">Geser elemen untuk mengatur posisi | Scroll pada gambar untuk ubah ukuran</span>
                </div>
            </div>

            <!-- Back Cover Preview -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Preview Sampul Belakang</h3>

                <div class="relative flex justify-center mb-4">
                    <!-- Back Book Container -->
                    <div id="backBookPreview" class="relative w-48 h-64 bg-gradient-to-b from-amber-700 to-amber-900 rounded-l-md shadow-lg overflow-hidden" style="box-shadow: -4px 4px 15px rgba(0,0,0,0.3);">
                        <!-- Spine (on left side for back cover) -->
                        <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-amber-900 to-amber-700"></div>

                        <!-- Back Cover Image Container -->
                        <div id="backCoverContainer" class="absolute inset-3 left-6 cursor-move flex items-center justify-center bg-white/10">
                            <span id="backCoverPlaceholder" class="text-white/50 text-xs text-center px-4">
                                Upload cover belakang
                            </span>
                            <img id="backCoverPreview" class="max-w-full max-h-full object-contain pointer-events-none" style="display: none;">
                            <div id="backResizeBorder" class="absolute inset-0 border-2 border-dashed border-gray-400/50 opacity-0 transition-opacity pointer-events-none" style="display: none;"></div>
                        </div>

                        <!-- Draggable Title for Back Cover -->
                        <div id="backTitleContainer" class="absolute top-4 left-0 right-0 text-center px-4 cursor-move select-none">
                            <span id="previewBackTitle" class="text-white text-xs font-semibold drop-shadow-md line-clamp-2">
                                Judul Buku
                            </span>
                        </div>

                        <!-- Back Additional Texts Container - Draggable -->
                        <div id="backAdditionalTextsPreview" class="absolute left-0 right-0 text-center px-4 cursor-move" style="bottom: 16px;">
                            <!-- Dynamic back text previews will be added here -->
                        </div>
                    </div>
                </div>

                <!-- Position Controls for Back Cover -->
                <div class="space-y-3">
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" id="backZoomOutBtn" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors" title="Perkecil">−</button>
                        <input type="range" id="backZoomSlider" min="30" max="250" value="100" class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <button type="button" id="backZoomInBtn" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors" title="Perbesar">+</button>
                        <span id="backZoomLevel" class="text-xs text-gray-500 ml-2 w-12">100%</span>
                    </div>
                    <div class="flex items-center justify-center gap-4">
                        <button type="button" id="resetBackPosition" class="text-xs text-gray-500 hover:text-gray-700 underline">
                            Reset Posisi
                        </button>
                        <span class="text-xs text-gray-400">|</span>
                        <span class="text-xs text-gray-500">Geser elemen untuk mengatur posisi | Scroll pada gambar untuk ubah ukuran</span>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for positions -->
            <input type="hidden" name="cover_position" id="coverPosition" value='{"x":0,"y":0}'>
            <input type="hidden" name="cover_scale" id="coverScale" value="1">
            <input type="hidden" name="title_position" id="titlePosition" value='{"x":0,"y":0}'>
            <input type="hidden" name="cover_texts" id="coverTexts" value='[]'>
            <input type="hidden" name="back_cover_position" id="backCoverPosition" value='{"x":0,"y":0}'>
            <input type="hidden" name="back_cover_scale" id="backCoverScale" value="1">
            <input type="hidden" name="back_title_position" id="backTitlePosition" value='{"x":0,"y":0}'>
            <input type="hidden" name="back_cover_texts" id="backCoverTexts" value='[]'>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const coverInput = document.getElementById('coverImageInput');
    const coverPreview = document.getElementById('coverPreview');
    const coverPlaceholder = document.getElementById('coverPlaceholder');
    const coverContainer = document.getElementById('coverContainer');
    const coverPositionInput = document.getElementById('coverPosition');
    const coverScaleInput = document.getElementById('coverScale');
    const resizeBorder = document.getElementById('resizeBorder');

    const titleInput = document.getElementById('bookTitle');
    const previewTitle = document.getElementById('previewTitle');
    const titleContainer = document.getElementById('titleContainer');
    const titlePositionInput = document.getElementById('titlePosition');

    const additionalTextsContainer = document.getElementById('additionalTextsContainer');
    const additionalTextsPreview = document.getElementById('additionalTextsPreview');
    const addTextBtn = document.getElementById('addTextBtn');
    const coverTextsInput = document.getElementById('coverTexts');
    const resetPositionBtn = document.getElementById('resetPosition');

    // Zoom controls
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    const zoomSlider = document.getElementById('zoomSlider');
    const zoomLevel = document.getElementById('zoomLevel');

    let textCounter = 0;
    let additionalTexts = [];

    // Cover drag state
    let isCoverDragging = false;
    let coverStartX, coverStartY;
    let coverX = 0, coverY = 0;

    // Title drag state
    let isTitleDragging = false;
    let titleStartX, titleStartY;
    let titleX = 0, titleY = 0;

    // Additional texts drag state
    let isTextDragging = false;
    let textDragStartX, textDragStartY;
    let currentTextId = null;

    // Cover resize state
    let currentScale = 1;
    let isResizing = false;

    // Handle image upload
    coverInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                coverX = 0;
                coverY = 0;
                currentScale = 1;

                coverPreview.src = e.target.result;
                coverPreview.style.display = 'block';
                if (coverPlaceholder) coverPlaceholder.style.display = 'none';
                if (resizeBorder) {
                    resizeBorder.style.display = 'block';
                    resizeBorder.style.opacity = '1';
                }
                updateCoverPosition(0, 0);
                updateCoverScale(1);
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle title change
    titleInput.addEventListener('input', function(e) {
        previewTitle.textContent = e.target.value || 'Judul Buku';
    });

    // Cover drag functionality
    coverContainer.addEventListener('mousedown', function(e) {
        if (!coverPreview.src || coverPreview.style.display === 'none') return;
        isCoverDragging = true;
        coverStartX = e.clientX - coverX;
        coverStartY = e.clientY - coverY;
        coverContainer.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', function(e) {
        if (isCoverDragging) {
            e.preventDefault();
            coverX = e.clientX - coverStartX;
            coverY = e.clientY - coverStartY;
            updateCoverPosition(coverX, coverY);
        }
        if (isTitleDragging) {
            e.preventDefault();
            titleX = e.clientX - titleStartX;
            titleY = e.clientY - titleStartY;
            updateTitlePosition(titleX, titleY);
        }
        if (isTextDragging && currentTextId !== null) {
            e.preventDefault();
            const textObj = additionalTexts.find(t => t.id === currentTextId);
            if (textObj) {
                const x = e.clientX - textDragStartX;
                const y = e.clientY - textDragStartY;
                updateTextPosition(currentTextId, x, y);
            }
        }
    });

    document.addEventListener('mouseup', function() {
        if (isCoverDragging) {
            isCoverDragging = false;
            coverContainer.style.cursor = 'move';
        }
        if (isTitleDragging) {
            isTitleDragging = false;
            titleContainer.style.cursor = 'move';
        }
        if (isTextDragging) {
            isTextDragging = false;
            const textPreview = document.getElementById(`textPreview_${currentTextId}`);
            if (textPreview) textPreview.style.cursor = 'move';
            currentTextId = null;
        }
    });

    // Title drag functionality
    titleContainer.addEventListener('mousedown', function(e) {
        e.preventDefault();
        isTitleDragging = true;
        titleStartX = e.clientX - titleX;
        titleStartY = e.clientY - titleY;
        titleContainer.style.cursor = 'grabbing';
    });

    // Touch support
    coverContainer.addEventListener('touchstart', function(e) {
        if (!coverPreview.src || coverPreview.style.display === 'none') return;
        isCoverDragging = true;
        const touch = e.touches[0];
        coverStartX = touch.clientX - coverX;
        coverStartY = touch.clientY - coverY;
    });

    titleContainer.addEventListener('touchstart', function(e) {
        isTitleDragging = true;
        const touch = e.touches[0];
        titleStartX = touch.clientX - titleX;
        titleStartY = touch.clientY - titleY;
    });

    // Add touch support for text previews via delegation
    additionalTextsPreview.addEventListener('touchstart', function(e) {
        const textPreview = e.target.closest('[data-text-id]');
        if (textPreview) {
            e.stopPropagation();
            isTextDragging = true;
            currentTextId = parseInt(textPreview.dataset.textId);
            const textObj = additionalTexts.find(t => t.id === currentTextId);
            const touch = e.touches[0];
            textDragStartX = touch.clientX - (textObj ? textObj.position.x : 0);
            textDragStartY = touch.clientY - (textObj ? textObj.position.y : 0);
        }
    });

    document.addEventListener('touchmove', function(e) {
        if (isCoverDragging) {
            const touch = e.touches[0];
            coverX = touch.clientX - coverStartX;
            coverY = touch.clientY - coverStartY;
            updateCoverPosition(coverX, coverY);
        }
        if (isTitleDragging) {
            const touch = e.touches[0];
            titleX = touch.clientX - titleStartX;
            titleY = touch.clientY - titleStartY;
            updateTitlePosition(titleX, titleY);
        }
        if (isTextDragging && currentTextId !== null) {
            const touch = e.touches[0];
            const textObj = additionalTexts.find(t => t.id === currentTextId);
            if (textObj) {
                const x = touch.clientX - textDragStartX;
                const y = touch.clientY - textDragStartY;
                updateTextPosition(currentTextId, x, y);
            }
        }
    });

    document.addEventListener('touchend', function() {
        isCoverDragging = false;
        isTitleDragging = false;
        isTextDragging = false;
        isResizing = false;
        currentTextId = null;
    });

    // Wheel resize functionality - scroll on image to resize
    coverContainer.addEventListener('wheel', function(e) {
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        const newScale = currentScale + delta;
        updateCoverScale(newScale);
    }, { passive: false });

    // Zoom button handlers
    zoomInBtn.addEventListener('click', function() {
        const newScale = currentScale + 0.1;
        updateCoverScale(newScale);
    });

    zoomOutBtn.addEventListener('click', function() {
        const newScale = currentScale - 0.1;
        updateCoverScale(newScale);
    });

    zoomSlider.addEventListener('input', function(e) {
        const scale = e.target.value / 100;
        updateCoverScale(scale);
    });

    // Touch pinch-to-zoom support
    let initialPinchDistance = null;
    coverContainer.addEventListener('touchstart', function(e) {
        if (e.touches.length === 2) {
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            initialPinchDistance = Math.sqrt(dx * dx + dy * dy);
        }
    });

    document.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2 && initialPinchDistance !== null) {
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            const currentDistance = Math.sqrt(dx * dx + dy * dy);
            const delta = (currentDistance - initialPinchDistance) / 200;
            const newScale = currentScale + delta;
            updateCoverScale(newScale);
            initialPinchDistance = currentDistance;
        }
    });

    document.addEventListener('touchend', function() {
        initialPinchDistance = null;
        isResizing = false;
    });

    // Add touch/mouse support delegation for text previews
    additionalTextsPreview.addEventListener('mousedown', function(e) {
        const textPreview = e.target.closest('[data-text-id]');
        if (textPreview) {
            e.stopPropagation();
            isTextDragging = true;
            currentTextId = parseInt(textPreview.dataset.textId);
            const textObj = additionalTexts.find(t => t.id === currentTextId);
            textDragStartX = e.clientX - (textObj ? textObj.position.x : 0);
            textDragStartY = e.clientY - (textObj ? textObj.position.y : 0);
            textPreview.style.cursor = 'grabbing';
        }
    });

    function updateCoverPosition(x, y) {
        coverPreview.style.transform = `translate(${x}px, ${y}px) scale(${currentScale})`;
        coverPositionInput.value = JSON.stringify({x: x, y: y});
    }

    function updateTitlePosition(x, y) {
        titleContainer.style.transform = `translate(${x}px, ${y}px)`;
        titlePositionInput.value = JSON.stringify({x: x, y: y});
    }

    function updateCoverScale(scale) {
        currentScale = Math.max(0.3, Math.min(2.5, scale));
        coverPreview.style.transform = `translate(${coverX}px, ${coverY}px) scale(${currentScale})`;
        coverScaleInput.value = currentScale;
        zoomSlider.value = currentScale * 100;
        zoomLevel.textContent = Math.round(currentScale * 100) + '%';
    }

    function updateTextPosition(textId, x, y) {
        const textObj = additionalTexts.find(t => t.id === textId);
        if (textObj) {
            textObj.position = {x: x, y: y};
            const preview = document.getElementById(`textPreview_${textId}`);
            if (preview) {
                preview.style.transform = `translate(${x}px, ${y}px)`;
            }
            updateCoverTexts();
        }
    }

    // Add additional text
    addTextBtn.addEventListener('click', function() {
        const textId = textCounter++;
        additionalTexts.push({ id: textId, text: '', position: {x: 0, y: 0} });

        // Add form field
        const textField = document.createElement('div');
        textField.className = 'flex items-center gap-2';
        textField.dataset.id = textId;
        textField.innerHTML = `
            <input type="text" name="cover_text_${textId}" placeholder="Teks tambahan ${textId + 1}"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                data-text-id="${textId}">
            <button type="button" class="remove-text-btn p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors" data-id="${textId}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        additionalTextsContainer.appendChild(textField);

        // Add preview with drag functionality
        const textPreview = document.createElement('span');
        textPreview.id = `textPreview_${textId}`;
        textPreview.className = 'block text-white/80 text-[10px] drop-shadow-md line-clamp-1 mt-1 cursor-move';
        textPreview.textContent = `Teks ${textId + 1}`;
        textPreview.dataset.textId = textId;
        additionalTextsPreview.appendChild(textPreview);

        // Add drag functionality for text
        textPreview.addEventListener('mousedown', function(e) {
            e.stopPropagation();
            isTextDragging = true;
            currentTextId = textId;
            const textObj = additionalTexts.find(t => t.id === textId);
            textDragStartX = e.clientX - (textObj ? textObj.position.x : 0);
            textDragStartY = e.clientY - (textObj ? textObj.position.y : 0);
            textPreview.style.cursor = 'grabbing';
        });

        // Event listener for input
        const input = textField.querySelector('input');
        input.addEventListener('input', function(e) {
            const textId = parseInt(this.dataset.textId);
            const textObj = additionalTexts.find(t => t.id === textId);
            if (textObj) {
                textObj.text = e.target.value;
                const preview = document.getElementById(`textPreview_${textId}`);
                if (preview) {
                    preview.textContent = e.target.value || `Teks ${textId + 1}`;
                }
                updateCoverTexts();
            }
        });

        // Event listener for remove
        const removeBtn = textField.querySelector('.remove-text-btn');
        removeBtn.addEventListener('click', function() {
            const textId = parseInt(this.dataset.id);
            removeText(textId);
        });

        updateCoverTexts();
    });

    function removeText(textId) {
        additionalTexts = additionalTexts.filter(t => t.id !== textId);

        const textField = additionalTextsContainer.querySelector(`[data-id="${textId}"]`);
        if (textField) textField.remove();

        const textPreview = document.getElementById(`textPreview_${textId}`);
        if (textPreview) textPreview.remove();

        updateCoverTexts();
    }

    function updateCoverTexts() {
        const texts = additionalTexts.map(t => ({
            text: t.text,
            position: t.position
        }));
        coverTextsInput.value = JSON.stringify(texts);
    }

    // Reset position
    resetPositionBtn.addEventListener('click', function() {
        coverX = 0;
        coverY = 0;
        titleX = 0;
        titleY = 0;

        updateCoverPosition(0, 0);
        updateCoverScale(1);
        updateTitlePosition(0, 0);

        additionalTexts.forEach(t => {
            t.position = {x: 0, y: 0};
        });
        updateCoverTexts();

        // Reset visual positions for additional texts
        additionalTexts.forEach(t => {
            const preview = document.getElementById(`textPreview_${t.id}`);
            if (preview) {
                preview.style.transform = 'translate(0, 0)';
            }
        });
    });

    // Thumbnail Generation
    const generateThumbnailBtn = document.getElementById('generateThumbnailBtn');
    const thumbnailPreviewContainer = document.getElementById('thumbnailPreviewContainer');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const generatedThumbnailInput = document.getElementById('generatedThumbnail');
    const removeThumbnailBtn = document.getElementById('removeThumbnail');
    const thumbnailInput = document.getElementById('thumbnailInput');

    if (generateThumbnailBtn) {
        generateThumbnailBtn.addEventListener('click', async function() {
            const bookPreview = document.getElementById('bookPreview');
            if (!bookPreview) {
                alert('Preview buku tidak ditemukan');
                return;
            }

            // Check if cover image exists
            if (!coverPreview.src || coverPreview.style.display === 'none') {
                alert('Silakan upload cover buku terlebih dahulu');
                return;
            }

            try {
                generateThumbnailBtn.disabled = true;
                generateThumbnailBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Generating...';

                const canvas = await html2canvas(bookPreview, {
                    backgroundColor: null,
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    logging: false
                });

                const dataUrl = canvas.toDataURL('image/png');
                thumbnailPreview.src = dataUrl;
                generatedThumbnailInput.value = dataUrl;
                thumbnailPreviewContainer.classList.remove('hidden');

                // Clear file input
                thumbnailInput.value = '';

            } catch (error) {
                console.error('Error generating thumbnail:', error);
                alert('Gagal membuat thumbnail: ' + error.message);
            } finally {
                generateThumbnailBtn.disabled = false;
                generateThumbnailBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Generate dari Preview';
            }
        });
    }

    // Remove thumbnail
    if (removeThumbnailBtn) {
        removeThumbnailBtn.addEventListener('click', function() {
            generatedThumbnailInput.value = '';
            thumbnailPreviewContainer.classList.add('hidden');
            thumbnailPreview.src = '';
        });
    }

    // ==================== Back Cover Functionality ====================
    const backCoverInput = document.getElementById('backCoverImageInput');
    const backCoverPreview = document.getElementById('backCoverPreview');
    const backCoverPlaceholder = document.getElementById('backCoverPlaceholder');
    const backCoverContainer = document.getElementById('backCoverContainer');
    const backCoverPositionInput = document.getElementById('backCoverPosition');
    const backCoverScaleInput = document.getElementById('backCoverScale');
    const backResizeBorder = document.getElementById('backResizeBorder');
    const previewBackTitle = document.getElementById('previewBackTitle');
    const backTitleContainer = document.getElementById('backTitleContainer');
    const backTitlePositionInput = document.getElementById('backTitlePosition');

    const backZoomInBtn = document.getElementById('backZoomInBtn');
    const backZoomOutBtn = document.getElementById('backZoomOutBtn');
    const backZoomSlider = document.getElementById('backZoomSlider');
    const backZoomLevel = document.getElementById('backZoomLevel');

    let backCoverX = 0, backCoverY = 0;
    let isBackCoverDragging = false;
    let backCoverStartX, backCoverStartY;
    let backCurrentScale = 1;

    let isBackTitleDragging = false;
    let backTitleStartX, backTitleStartY;
    let backTitleX = 0, backTitleY = 0;

    // Handle back cover image upload
    if (backCoverInput) {
        backCoverInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    backCoverX = 0;
                    backCoverY = 0;
                    backCurrentScale = 1;

                    backCoverPreview.src = e.target.result;
                    backCoverPreview.style.display = 'block';
                    if (backCoverPlaceholder) backCoverPlaceholder.style.display = 'none';
                    if (backResizeBorder) {
                        backResizeBorder.style.display = 'block';
                        backResizeBorder.style.opacity = '1';
                    }
                    updateBackCoverPosition(0, 0);
                    updateBackCoverScale(1);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Sync back title
    const backBookTitleInput = document.getElementById('backBookTitle');
    if (backBookTitleInput) {
        backBookTitleInput.addEventListener('input', function(e) {
            previewBackTitle.textContent = e.target.value || 'Judul Buku';
        });
    }

    // Back cover drag functionality
    if (backCoverContainer) {
        backCoverContainer.addEventListener('mousedown', function(e) {
            if (!backCoverPreview.src || backCoverPreview.style.display === 'none') return;
            isBackCoverDragging = true;
            backCoverStartX = e.clientX - backCoverX;
            backCoverStartY = e.clientY - backCoverY;
            backCoverContainer.style.cursor = 'grabbing';
        });
    }

    if (backTitleContainer) {
        backTitleContainer.addEventListener('mousedown', function(e) {
            e.preventDefault();
            isBackTitleDragging = true;
            backTitleStartX = e.clientX - backTitleX;
            backTitleStartY = e.clientY - backTitleY;
            backTitleContainer.style.cursor = 'grabbing';
        });
    }

    document.addEventListener('mousemove', function(e) {
        if (isBackCoverDragging) {
            e.preventDefault();
            backCoverX = e.clientX - backCoverStartX;
            backCoverY = e.clientY - backCoverStartY;
            updateBackCoverPosition(backCoverX, backCoverY);
        }
        if (isBackTitleDragging) {
            e.preventDefault();
            backTitleX = e.clientX - backTitleStartX;
            backTitleY = e.clientY - backTitleStartY;
            updateBackTitlePosition(backTitleX, backTitleY);
        }
    });

    document.addEventListener('mouseup', function() {
        if (isBackCoverDragging) {
            isBackCoverDragging = false;
            if (backCoverContainer) backCoverContainer.style.cursor = 'move';
        }
        if (isBackTitleDragging) {
            isBackTitleDragging = false;
            if (backTitleContainer) backTitleContainer.style.cursor = 'move';
        }
    });

    // Wheel resize for back cover
    if (backCoverContainer) {
        backCoverContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            const newScale = backCurrentScale + delta;
            updateBackCoverScale(newScale);
        }, { passive: false });
    }

    // Back cover zoom buttons
    if (backZoomInBtn) {
        backZoomInBtn.addEventListener('click', function() {
            const newScale = backCurrentScale + 0.1;
            updateBackCoverScale(newScale);
        });
    }

    if (backZoomOutBtn) {
        backZoomOutBtn.addEventListener('click', function() {
            const newScale = backCurrentScale - 0.1;
            updateBackCoverScale(newScale);
        });
    }

    if (backZoomSlider) {
        backZoomSlider.addEventListener('input', function(e) {
            const scale = e.target.value / 100;
            updateBackCoverScale(scale);
        });
    }

    function updateBackCoverPosition(x, y) {
        if (backCoverPreview) {
            backCoverPreview.style.transform = `translate(${x}px, ${y}px) scale(${backCurrentScale})`;
        }
        if (backCoverPositionInput) {
            backCoverPositionInput.value = JSON.stringify({x: x, y: y});
        }
    }

    function updateBackTitlePosition(x, y) {
        if (backTitleContainer) {
            backTitleContainer.style.transform = `translate(${x}px, ${y}px)`;
        }
        if (backTitlePositionInput) {
            backTitlePositionInput.value = JSON.stringify({x: x, y: y});
        }
    }

    function updateBackCoverScale(scale) {
        backCurrentScale = Math.max(0.3, Math.min(2.5, scale));
        if (backCoverPreview) {
            backCoverPreview.style.transform = `translate(${backCoverX}px, ${backCoverY}px) scale(${backCurrentScale})`;
        }
        if (backCoverScaleInput) {
            backCoverScaleInput.value = backCurrentScale;
        }
        if (backZoomSlider) {
            backZoomSlider.value = backCurrentScale * 100;
        }
        if (backZoomLevel) {
            backZoomLevel.textContent = Math.round(backCurrentScale * 100) + '%';
        }
    }

    // Reset back cover position
    const resetBackPositionBtn = document.getElementById('resetBackPosition');
    if (resetBackPositionBtn) {
        resetBackPositionBtn.addEventListener('click', function() {
            backCoverX = 0;
            backCoverY = 0;
            backTitleX = 0;
            backTitleY = 0;

            updateBackCoverPosition(0, 0);
            updateBackCoverScale(1);
            updateBackTitlePosition(0, 0);
        });
    }

    // ==================== Back Cover Additional Texts ====================
    const backAdditionalTextsContainer = document.getElementById('backAdditionalTextsContainer');
    const backAdditionalTextsPreview = document.getElementById('backAdditionalTextsPreview');
    const addBackTextBtn = document.getElementById('addBackTextBtn');
    const backCoverTextsInput = document.getElementById('backCoverTexts');

    let backTextCounter = 0;
    let backAdditionalTexts = [];

    if (addBackTextBtn) {
        addBackTextBtn.addEventListener('click', function() {
            const textId = backTextCounter++;
            backAdditionalTexts.push({ id: textId, text: '', position: {x: 0, y: 0} });

            const textField = document.createElement('div');
            textField.className = 'flex items-center gap-2';
            textField.dataset.id = textId;
            textField.innerHTML = `
                <input type="text" name="back_cover_text_${textId}" placeholder="Teks tambahan ${textId + 1}"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    data-back-text-id="${textId}">
                <button type="button" class="remove-back-text-btn p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors" data-id="${textId}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            if (backAdditionalTextsContainer) {
                backAdditionalTextsContainer.appendChild(textField);

                // Add preview text in back cover preview
                const textPreview = document.createElement('span');
                textPreview.id = `backTextPreview_${textId}`;
                textPreview.className = 'block text-white/80 text-[10px] drop-shadow-md line-clamp-1 mt-1 cursor-move';
                textPreview.textContent = `Teks ${textId + 1}`;
                textPreview.dataset.backTextId = textId;
                if (backAdditionalTextsPreview) {
                    backAdditionalTextsPreview.appendChild(textPreview);
                }

                const input = textField.querySelector('input');
                input.addEventListener('input', function(e) {
                    const textId = parseInt(this.dataset.backTextId);
                    const textObj = backAdditionalTexts.find(t => t.id === textId);
                    if (textObj) {
                        textObj.text = e.target.value;
                        const preview = document.getElementById(`backTextPreview_${textId}`);
                        if (preview) {
                            preview.textContent = e.target.value || `Teks ${textId + 1}`;
                        }
                        updateBackCoverTexts();
                    }
                });

                const removeBtn = textField.querySelector('.remove-back-text-btn');
                removeBtn.addEventListener('click', function() {
                    const textId = parseInt(this.dataset.id);
                    backAdditionalTexts = backAdditionalTexts.filter(t => t.id !== textId);
                    textField.remove();
                    const preview = document.getElementById(`backTextPreview_${textId}`);
                    if (preview) preview.remove();
                    updateBackCoverTexts();
                });
            }

            updateBackCoverTexts();
        });
    }

    function updateBackCoverTexts() {
        if (backCoverTextsInput) {
            const texts = backAdditionalTexts.map(t => ({
                text: t.text,
                position: t.position
            }));
            backCoverTextsInput.value = JSON.stringify(texts);
        }
    }
});
</script>
@endpush

<style>
.select-none {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
