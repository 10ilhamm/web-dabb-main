@extends('layouts.app')

@section('breadcrumb_parent', __('cms.virtual_book_pages.breadcrumb_parent'))
@section('breadcrumb_active', 'Edit Buku')

@section('content')
<div class="mb-4">
    <a href="{{ route('cms.features.virtual_books.index', $feature) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm" style="background-color: #818284;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali ke Daftar Buku
    </a>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Buku: {{ $book->title }}</h1>
    <p class="text-sm text-gray-500 mt-1">Perbarui pengaturan cover buku</p>
</div>

<form action="{{ route('cms.features.virtual_books.update', [$feature, $book]) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="bookForm">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Pengaturan Buku</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="bookTitle" value="{{ old('title', $book->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                </div>

                <!-- Cover Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                    @if($book->cover_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover" class="w-32 h-40 object-cover rounded-lg border border-gray-200">
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" name="remove_cover_image" value="1" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-500">Hapus cover</span>
                    </label>
                    <hr class="my-3 border-gray-200">
                    @endif
                    <input type="file" name="cover_image" id="coverImageInput" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1.5">JPG, PNG, atau WebP.</p>
                </div>

                <!-- Additional Texts Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teks Tambahan (Opsional)</label>
                    <p class="text-xs text-gray-500 mb-2">Tambahkan teks seperti subjudul atau deskripsi sampul</p>

                    <div id="additionalTextsContainer" class="space-y-2">
                        @php $coverTextsArray = is_string($book->cover_texts) ? json_decode($book->cover_texts, true) : ($book->cover_texts ?? []); @endphp
                        @if($coverTextsArray && count($coverTextsArray) > 0)
                            @foreach($coverTextsArray as $index => $coverText)
                            <div class="flex items-center gap-2" data-id="{{ $index }}">
                                <input type="text" name="cover_text_{{ $index }}" value="{{ $coverText['text'] ?? '' }}" placeholder="Teks tambahan {{ $index + 1 }}"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" data-text-id="{{ $index }}">
                                <button type="button" class="remove-text-btn p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors" data-id="{{ $index }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <button type="button" id="addTextBtn" class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Teks
                    </button>
                </div>

                <!-- Thumbnail -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Daftar</label>
                    @if($book->thumbnail)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $book->thumbnail) }}" alt="Thumbnail" class="w-24 h-32 object-cover rounded-lg border border-gray-200">
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" name="remove_thumbnail" value="1" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-500">Hapus thumbnail</span>
                    </label>
                    <hr class="my-3 border-gray-200">
                    @endif
                    <input type="file" name="thumbnail" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan <span class="text-red-500">*</span></label>
                    <input type="number" name="order" value="{{ old('order', $book->order) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('cms.features.virtual_books.index', $feature) }}" class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm" style="background-color:#1d4ed8;">
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- Right Column: Preview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Preview Cover Buku</h3>

            <div class="relative flex justify-center">
                <!-- Book Container -->
                <div id="bookPreview" class="relative w-48 h-64 bg-gradient-to-b from-amber-700 to-amber-900 rounded-r-md shadow-lg overflow-hidden" style="box-shadow: 4px 4px 15px rgba(0,0,0,0.3);">
                    <!-- Spine -->
                    <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-amber-900 to-amber-700"></div>

                    <!-- Cover Image Container - Draggable & Resizable -->
                    <div id="coverContainer" class="absolute inset-3 left-6 cursor-move flex items-center justify-center bg-white/10">
                        @if($book->cover_image)
                        <img id="coverPreview" src="{{ asset('storage/' . $book->cover_image) }}" class="max-w-full max-h-full object-contain pointer-events-none" style="transform: translate({{ $book->cover_position['x'] ?? 0 }}px, {{ $book->cover_position['y'] ?? 0 }}px) scale({{ $book->cover_scale ?? 1 }});">
                        @else
                        <span id="coverPlaceholder" class="text-white/50 text-xs text-center px-4">
                            Upload cover untuk preview
                        </span>
                        <img id="coverPreview" class="max-w-full max-h-full object-contain pointer-events-none" style="display: none;">
                        @endif
                        <!-- Resize Border -->
                        <div id="resizeBorder" class="absolute inset-0 border-2 border-dashed border-gray-400/50 opacity-0 transition-opacity pointer-events-none" style="{{ $book->cover_image ? 'display: block; opacity: 1;' : 'display: none;' }}"></div>
                    </div>

                    <!-- Draggable Title -->
                    <div id="titleContainer" class="absolute bottom-4 left-0 right-0 text-center px-4 cursor-move" style="transform: translate({{ $book->title_position['x'] ?? 0 }}px, {{ $book->title_position['y'] ?? 0 }}px);">
                        <span id="previewTitle" class="text-white text-xs font-semibold drop-shadow-md line-clamp-2">
                            {{ $book->title }}
                        </span>
                    </div>

                    <!-- Additional Texts Container -->
                    <div id="additionalTextsPreview" class="absolute left-0 right-0 text-center px-4" style="bottom: 16px;">
                        @php $coverTextsArray = is_string($book->cover_texts) ? json_decode($book->cover_texts, true) : ($book->cover_texts ?? []); @endphp
                        @if($coverTextsArray && count($coverTextsArray) > 0)
                            @foreach($coverTextsArray as $index => $coverText)
                            <span id="textPreview_{{ $index }}" class="block text-white/80 text-[10px] drop-shadow-md line-clamp-1 mt-1 cursor-move" data-text-id="{{ $index }}" style="transform: translate({{ $coverText['position']['x'] ?? 0 }}px, {{ $coverText['position']['y'] ?? 0 }}px);">
                                {{ $coverText['text'] ?? "Teks " . ($index + 1) }}
                            </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Position Controls -->
            <div class="mt-4 space-y-3">
                <!-- Zoom Controls -->
                <div class="flex items-center justify-center gap-2">
                    <button type="button" id="zoomOutBtn" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors" title="Perkecil">−</button>
                    <input type="range" id="zoomSlider" min="30" max="250" value="{{ ($book->cover_scale ?? 1) * 100 }}" class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    <button type="button" id="zoomInBtn" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors" title="Perbesar">+</button>
                    <span id="zoomLevel" class="text-xs text-gray-500 ml-2 w-12">{{ round(($book->cover_scale ?? 1) * 100) }}%</span>
                </div>
                <div class="flex items-center justify-center gap-4">
                    <button type="button" id="resetPosition" class="text-xs text-gray-500 hover:text-gray-700 underline">
                        Reset Posisi
                    </button>
                    <span class="text-xs text-gray-400">|</span>
                    <span class="text-xs text-gray-500">Geser elemen untuk mengatur posisi | Scroll pada gambar untuk ubah ukuran</span>
                </div>
            </div>

            <!-- Hidden fields for positions -->
            <input type="hidden" name="cover_position" id="coverPosition" value='{{ json_encode($book->cover_position ?? ["x" => 0, "y" => 0]) }}'>
            <input type="hidden" name="cover_scale" id="coverScale" value="{{ $book->cover_scale ?? 1 }}">
            <input type="hidden" name="title_position" id="titlePosition" value='{{ json_encode($book->title_position ?? ["x" => 0, "y" => 0]) }}'>
            <input type="hidden" name="cover_texts" id="coverTexts" value='{{ json_encode($book->cover_texts ?? []) }}'>
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

    // Get initial texts from database
    let initialTexts = {{ $book->cover_texts ? json_encode($book->cover_texts) : '[]' }};
    let textCounter = initialTexts.length;
    let additionalTexts = initialTexts.map((t, i) => ({ id: i, text: t.text || '', position: t.position || {x: 0, y: 0} }));

    // Cover drag state
    let isCoverDragging = false;
    let coverStartX, coverStartY;
    let coverX = {{ $book->cover_position['x'] ?? 0 }};
    let coverY = {{ $book->cover_position['y'] ?? 0 }};

    // Title drag state
    let isTitleDragging = false;
    let titleStartX, titleStartY;
    let titleX = {{ $book->title_position['x'] ?? 0 }};
    let titleY = {{ $book->title_position['y'] ?? 0 }};

    // Additional texts drag state
    let isTextDragging = false;
    let textDragStartX, textDragStartY;
    let currentTextId = null;

    // Cover resize state
    let currentScale = {{ $book->cover_scale ?? 1 }};

    coverInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                coverPreview.src = e.target.result;
                coverPreview.style.display = 'block';
                if (coverPlaceholder) coverPlaceholder.style.display = 'none';
                updateCoverPosition(0, 0);
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
            const scaleDelta = (deltaX + deltaY) / 200;
            const newScale = currentScale + scaleDelta;
            updateCoverScale(newScale);
            resizeStartX = touch.clientX;
            resizeStartY = touch.clientY;
        }
    });

    document.addEventListener('touchend', function() {
        isResizing = false;
    });

    function updateCoverPosition(x, y) {
        // Remove offset limits for free movement, preserve scale
        coverPreview.style.transform = `translate(${x}px, ${y}px) scale(${currentScale})`;
        coverPositionInput.value = JSON.stringify({x: x, y: y});
    }

    function updateTitlePosition(x, y) {
        // Remove offset limits for free movement
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

    // Setup existing text fields
    additionalTextsContainer.querySelectorAll('input[data-text-id]').forEach(input => {
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
    });

    additionalTextsContainer.querySelectorAll('.remove-text-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const textId = parseInt(this.dataset.id);
            removeText(textId);
        });
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
});
</script>
@endpush

<style>
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