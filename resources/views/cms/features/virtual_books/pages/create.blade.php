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
        position: relative;
        overflow: visible;
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

    /* Draggable elements */
    .draggable-container {
        position: absolute;
        left: 35px;
        right: 20px;
        top: 50px;
        bottom: 30px;
    }

    .draggable-element {
        position: absolute;
        cursor: move;
        user-select: none;
        transition: box-shadow 0.2s;
    }

    .draggable-element:hover {
        outline: 2px dashed #3b82f6;
        outline-offset: 2px;
    }

    .draggable-element.dragging {
        opacity: 0.8;
        z-index: 100;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }

    .draggable-image {
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 4px;
    }

    .draggable-text {
        font-size: 0.65rem;
        text-align: justify;
        color: #3a2a1a;
        line-height: 1.4;
        padding: 8px;
        background: rgba(255,255,255,0.3);
        border-radius: 4px;
        overflow: auto;
        min-width: 30%;
        max-width: 100%;
        min-height: 20%;
    }

    /* Resize handle for text */
    .resize-handle {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 15px;
        height: 15px;
        cursor: se-resize;
        background: linear-gradient(135deg, transparent 50%, rgba(59, 130, 246, 0.5) 50%);
        border-radius: 0 0 4px 0;
    }

    .resize-handle:hover {
        background: linear-gradient(135deg, transparent 50%, rgba(59, 130, 246, 0.8) 50%);
    }

    .content-page-footer {
        font-size: 0.6rem;
        text-align: center;
        color: #8a7a6a;
        padding-top: 8px;
        border-top: 1px solid #d4c4a8;
        position: absolute;
        bottom: 8px;
        left: 35px;
        right: 20px;
    }

    /* Image thumbnails in form */
    .image-thumbnails {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 8px;
        margin-top: 10px;
    }

    .image-thumbnail {
        position: relative;
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
    }

    .image-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-thumbnail .remove-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 20px;
        height: 20px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        line-height: 1;
    }

    .image-thumbnail .position-label {
        position: absolute;
        bottom: 2px;
        left: 2px;
        background: rgba(0,0,0,0.6);
        color: white;
        font-size: 8px;
        padding: 2px 4px;
        border-radius: 2px;
    }
</style>

@section('breadcrumb_parent', __('cms.virtual_book_pages.breadcrumb_parent'))
@section('breadcrumb_active', __('cms.virtual_book_pages.breadcrumb_create'))

@section('content')
<div class="mb-4">
    <a href="{{ route('cms.features.virtual_books.pages.index', [$feature, $book]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm" style="background-color: #818284;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali ke Daftar
    </a>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Halaman Buku</h1>
    <p class="text-sm text-gray-500 mt-1">Tambahkan halaman baru untuk buku virtual</p>
</div>

<form action="{{ route('cms.features.virtual_books.pages.store', [$feature, $book]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="flex gap-6 items-start" style="flex-wrap: nowrap;">

        <!-- Left Column: Form Fields -->
        <div class="space-y-6" style="width: 38%; min-width: 380px; flex-shrink: 0;">

            <!-- Multiple Image Upload Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Gambar Halaman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar (Bisa Banyak)</label>
                        <input type="file" name="images[]" accept="image/*" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" onchange="handleImageUpload(this)">
                        <p class="text-xs text-gray-500 mt-1.5">JPG, PNG, atau WebP. Maks 2MB per gambar. Bisa upload beberapa gambar sekaligus.</p>
                    </div>

                    <!-- Image Thumbnails -->
                    <div id="imageThumbnails" class="image-thumbnails"></div>

                </div>
            </div>

            <!-- Page Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Informasi Halaman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman</label>
                        <input type="text" name="title" id="pageTitleInput" value="{{ old('title') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Masukkan judul halaman" oninput="updatePreview()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten Teks</label>
                        <textarea name="content" id="pageContentInput" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Masukkan konten teks halaman" oninput="updatePreview()">{{ old('content') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran Gambar (%)</label>
                        <div class="flex items-center gap-3">
                            <input type="range" name="image_height" id="imageHeightSlider" min="10" max="100" value="50" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('imageHeightValue').textContent = this.value + '%'; updatePreview()">
                            <span id="imageHeightValue" class="text-sm text-gray-600 w-12 text-right">50%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Atur tinggi gambar dalam halaman</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan <span class="text-red-500">*</span></label>
                        <input type="number" name="order" value="{{ $maxOrder + 1 }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <p class="text-xs text-gray-500 mt-1">Urutan tampilan halaman dalam buku</p>
                    </div>
                </div>
            </div>

            <!-- Thumbnail Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Thumbnail Halaman</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Thumbnail</label>
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
                        <p class="text-xs text-gray-500 mt-1.5">Atau upload manual. Generate akan membuat thumbnail dari preview halaman.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('cms.features.virtual_books.pages.index', [$feature, $book]) }}" class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
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
                <p class="text-xs text-gray-500 mb-2">Geser langsung elemen di preview dengan cursor</p>

                <div class="book-preview-container">
                    <div class="book-preview-wrapper">
                        <div id="bookPreview" class="book-preview content-page">
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
<script>
    let newImages = [];
    let imagePositions = [];
    let textPosition = { x: 0, y: 0, width: 45, height: 30 };

    function handleImageUpload(input) {
        const files = input.files;
        if (files && files.length > 0) {
            newImages = [];
            imagePositions = [];

            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    newImages.push(e.target.result);
                    imagePositions.push({ x: 0, y: 0 });
                    updateThumbnails();
                    updatePreview();
                };
                reader.readAsDataURL(file);
            });
        }
    }

    function updateThumbnails() {
        const container = document.getElementById('imageThumbnails');
        container.innerHTML = '';

        newImages.forEach((src, index) => {
            const div = document.createElement('div');
            div.className = 'image-thumbnail';
            div.innerHTML = `
                <img src="${src}" alt="Image ${index + 1}">
                <button type="button" class="remove-btn" onclick="removeImage(${index})">&times;</button>
                <div class="position-label">Img ${index + 1}</div>
            `;
            container.appendChild(div);
        });
    }

    function removeImage(index) {
        newImages.splice(index, 1);
        imagePositions.splice(index, 1);
        updateThumbnails();
        updatePreview();
    }

    function updatePreview() {
        const title = document.getElementById('pageTitleInput').value;
        const content = document.getElementById('pageContentInput').value;
        const imageHeight = document.getElementById('imageHeightSlider').value;
        const hasImages = newImages && newImages.length > 0;

        const preview = document.getElementById('bookPreview');
        preview.className = 'book-preview content-page';
        preview.innerHTML = '';

        let innerContent = '<div class="content-page-inner">';

        // Header (title)
        if (title) {
            innerContent += `<div class="content-page-header">${title}</div>`;
        }

        // Draggable container
        innerContent += '<div class="draggable-container" id="draggableContainer">';

        // Images (draggable)
        if (hasImages) {
            newImages.forEach((src, index) => {
                const pos = imagePositions[index] || { x: 0, y: 0 };
                const size = Math.max(20, parseInt(imageHeight));
                innerContent += `
                    <div class="draggable-element draggable-image"
                         data-type="image"
                         data-index="${index}"
                         style="background-image: url('${src}'); width: ${size}%; height: ${size * 0.75}%; left: ${pos.x}%; top: ${pos.y}%;"
                         onmousedown="startDrag(event, this)">
                    </div>`;
            });
        }

        // Text (draggable and resizable)
        if (content) {
            const textWidth = textPosition.width || 45;
            const textHeight = textPosition.height || 30;
            innerContent += `
                <div class="draggable-element draggable-text"
                     data-type="text"
                     style="width: ${textWidth}%; height: ${textHeight}%; left: ${textPosition.x}%; top: ${textPosition.y}%;"
                     onmousedown="startDrag(event, this)">
                    ${content.replace(/\n/g, '<br>')}
                    <div class="resize-handle" onmousedown="startResize(event, this.parentElement)"></div>
                </div>`;
        }

        innerContent += '</div>';

        // Footer
        innerContent += `<div class="content-page-footer">{{ $maxOrder + 1 }}</div>`;

        innerContent += '</div>';
        preview.innerHTML = innerContent;
    }

    // Drag functionality
    let draggedElement = null;
    let dragOffset = { x: 0, y: 0 };
    let dragContainer = null;

    function startDrag(e, element) {
        e.preventDefault();
        draggedElement = element;
        dragContainer = document.getElementById('draggableContainer');

        const rect = element.getBoundingClientRect();
        dragOffset.x = e.clientX - rect.left;
        dragOffset.y = e.clientY - rect.top;

        element.classList.add('dragging');

        document.addEventListener('mousemove', onDrag);
        document.addEventListener('mouseup', stopDrag);
    }

    function onDrag(e) {
        if (!draggedElement || !dragContainer) return;

        const containerRect = dragContainer.getBoundingClientRect();
        let newX = ((e.clientX - containerRect.left - dragOffset.x) / containerRect.width) * 100;
        let newY = ((e.clientY - containerRect.top - dragOffset.y) / containerRect.height) * 100;

        newX = Math.max(0, Math.min(100 - parseFloat(draggedElement.style.width) || 50, newX));
        newY = Math.max(0, Math.min(100 - parseFloat(draggedElement.style.height) || 30, newY));

        draggedElement.style.left = newX + '%';
        draggedElement.style.top = newY + '%';

        const type = draggedElement.dataset.type;
        const index = parseInt(draggedElement.dataset.index);

        if (type === 'image') {
            imagePositions[index] = { x: newX, y: newY };
        } else if (type === 'text') {
            textPosition = { ...textPosition, x: newX, y: newY };
        }
    }

    function stopDrag() {
        if (draggedElement) {
            draggedElement.classList.remove('dragging');
            draggedElement = null;
        }
        document.removeEventListener('mousemove', onDrag);
        document.removeEventListener('mouseup', stopDrag);
        updatePositionInputs();
    }

    // Resize functionality for text
    let resizingElement = null;
    let resizeStartPos = { x: 0, y: 0 };
    let resizeStartSize = { width: 0, height: 0 };

    function startResize(e, element) {
        e.preventDefault();
        e.stopPropagation();
        resizingElement = element;
        dragContainer = document.getElementById('draggableContainer');

        resizeStartPos.x = e.clientX;
        resizeStartPos.y = e.clientY;
        resizeStartSize.width = parseFloat(element.style.width) || 45;
        resizeStartSize.height = parseFloat(element.style.height) || 30;

        element.classList.add('dragging');

        document.addEventListener('mousemove', onResize);
        document.addEventListener('mouseup', stopResize);
    }

    function onResize(e) {
        if (!resizingElement || !dragContainer) return;

        const containerRect = dragContainer.getBoundingClientRect();
        const deltaX = e.clientX - resizeStartPos.x;
        const deltaY = e.clientY - resizeStartPos.y;

        const newWidth = Math.max(20, Math.min(100, resizeStartSize.width + (deltaX / containerRect.width) * 100));
        const newHeight = Math.max(15, Math.min(100, resizeStartSize.height + (deltaY / containerRect.height) * 100));

        resizingElement.style.width = newWidth + '%';
        resizingElement.style.height = newHeight + '%';
    }

    function stopResize() {
        if (resizingElement) {
            textPosition.width = parseFloat(resizingElement.style.width) || 45;
            textPosition.height = parseFloat(resizingElement.style.height) || 30;

            resizingElement.classList.remove('dragging');
            resizingElement = null;
        }
        document.removeEventListener('mousemove', onResize);
        document.removeEventListener('mouseup', stopResize);
        updatePositionInputs();
    }

    function updatePositionInputs() {
        const container = document.getElementById('positionInputs');
        let html = '';

        imagePositions.forEach((pos, index) => {
            html += `<input type="hidden" name="image_positions[${index}][x]" value="${pos.x}">`;
            html += `<input type="hidden" name="image_positions[${index}][y]" value="${pos.y}">`;
        });

        html += `<input type="hidden" name="text_position[x]" value="${textPosition.x}">`;
        html += `<input type="hidden" name="text_position[y]" value="${textPosition.y}">`;
        html += `<input type="hidden" name="text_position[width]" value="${textPosition.width || 45}">`;
        html += `<input type="hidden" name="text_position[height]" value="${textPosition.height || 30}">`;

        container.innerHTML = html;
    }

    // Thumbnail generation
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();

        const generateBtn = document.getElementById('generateThumbnailBtn');
        const generatedInput = document.getElementById('generatedThumbnail');
        const previewContainer = document.getElementById('thumbnailPreviewContainer');
        const previewImg = document.getElementById('thumbnailPreview');
        const removeBtn = document.getElementById('removeThumbnail');

        generateBtn.addEventListener('click', async function() {
            const bookPreview = document.getElementById('bookPreview');
            if (!bookPreview) return;

            try {
                generateBtn.disabled = true;
                generateBtn.textContent = 'Generating...';

                const canvas = await html2canvas(bookPreview, {
                    backgroundColor: null,
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    logging: false
                });

                const dataUrl = canvas.toDataURL('image/png');
                generatedInput.value = dataUrl;
                previewImg.src = dataUrl;
                previewContainer.classList.remove('hidden');

                // Clear file input if any
                document.getElementById('thumbnailInput').value = '';
            } catch (err) {
                alert('Gagal generate thumbnail: ' + err.message);
            } finally {
                generateBtn.disabled = false;
                generateBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Generate dari Preview
                `;
            }
        });

        removeBtn.addEventListener('click', function() {
            generatedInput.value = '';
            previewImg.src = '';
            previewContainer.classList.add('hidden');
        });
    });
</script>
@endpush
