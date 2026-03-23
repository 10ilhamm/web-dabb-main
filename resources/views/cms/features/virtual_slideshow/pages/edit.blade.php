@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    @if ($feature->parent)
        @php
            $grandparent = $feature->parent->parent;
        @endphp

        @if ($grandparent && $grandparent->id !== $feature->parent->id)
            <span class="text-gray-300">/</span>
            <a href="{{ url('/cms/features/' . $grandparent->id . '/') }}"
                class="text-gray-400 hover:text-gray-600 transition-colors">{{ $grandparent->name }}</a>
        @endif

        <span class="text-gray-300">/</span>
        <a href="{{ url('/cms/features/' . $feature->parent->id . '/') }}"
            class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->parent->name }}</a>
    @endif
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.show', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.slideshow.pages.slides.index', [$feature, $page]) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $page->title }}</a>
@endsection
@section('breadcrumb_active', 'Edit Slide')

@push('styles')
    <link rel="stylesheet" href="https://richtexteditor.com/richtexteditor/rte_theme_default.css" />
    <style>
        .slide-type-card {
            cursor: pointer;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s;
        }

        .slide-type-card:hover {
            border-color: #174E93;
            background: #f0f5ff;
        }

        .slide-type-card.active {
            border-color: #174E93;
            background: #eef3ff;
        }

        .slide-type-card .icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .slide-type-card .label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
        }

        .slide-type-card .desc {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s;
        }

        .form-input:focus {
            border-color: #174E93;
            box-shadow: 0 0 0 3px rgba(23, 78, 147, 0.1);
        }

        .info-popup-row {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
        }

        .existing-img-wrap {
            position: relative;
            display: inline-block;
            margin: 4px;
        }

        .existing-img-wrap img {
            height: 80px;
            width: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .existing-img-wrap .remove-existing {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .img-preview-wrap {
            position: relative;
            display: inline-block;
        }

        .img-preview-wrap img {
            height: 60px;
            width: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .img-preview-wrap .remove-img {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        #imagePreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #newImagePreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #carouselVideoPreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #urlImagePreviewArea {
            display: none;
        }

        #newImagePreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #carouselVideoUrlPreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #carouselVideoPreviewArea {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6" x-data="{ deleteVideoModal: false, deleteUrlModal: false, deleteUrlIndex: null }">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
                style="background-color: #818284;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Slide</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
                @if (isset($page))
                    <p class="text-sm text-blue-600 mt-0.5">Halaman: {{ $page->title }}</p>
                @endif
            </div>
        </div>

        <form
            action="{{ isset($page) ? route('cms.features.slideshow.pages.slides.update', [$feature, $page, $slide]) : route('cms.features.slideshow.update', [$feature, $slide]) }}"
            method="POST" enctype="multipart/form-data" id="slideForm"
            data-redirect="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}">
            @csrf @method('PUT')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <div class="font-semibold">Terdapat kesalahan:</div>
                    <ul class="list-disc list-inside mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (isset($page))
                <input type="hidden" name="feature_page_id" value="{{ $page->id }}">
            @endif

            {{-- Tipe Slide --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">1. Tipe Slide</h2>
                <input type="hidden" name="slide_type" id="slide_type_input" value="{{ $slide->slide_type }}">
                <input type="hidden" name="unified_video_order" id="unifiedVideoOrder">
                <input type="hidden" name="existing_carousel_videos" id="existingCarouselVideosInput">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    @foreach (['text' => ['📝', 'Teks', 'Konten teks saja'], 'hero' => ['🌟', 'Hero', 'Banner pembuka'], 'carousel' => ['🖼️', 'Carousel', 'Slideshow gambar'], 'video' => ['🎬', 'Video', 'Embed video'], 'text_carousel' => ['📋', 'Teks + Carousel', 'Split layout']] as $type => $info)
                        <div class="slide-type-card {{ $slide->slide_type === $type ? 'active' : '' }}"
                            data-type="{{ $type }}" onclick="selectType('{{ $type }}')">
                            <div class="icon">{{ $info[0] }}</div>
                            <div class="label">{{ $info[1] }}</div>
                            <div class="desc">{{ $info[2] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Konten --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4">
                <h2 class="text-base font-semibold text-gray-800">2. Konten</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-input" value="{{ old('title', $slide->title) }}">
                    </div>
                    <div>
                        <label class="form-label">Sub-judul</label>
                        <input type="text" name="subtitle" class="form-input"
                            value="{{ old('subtitle', $slide->subtitle) }}">
                    </div>
                </div>
                <div>
                    <label class="form-label">Deskripsi / Teks Konten <span class="text-gray-400 text-xs">(gunakan toolbar
                            untuk format)</span></label>
                    <div id="div_editor1" style="min-width:100%;">{!! old('description', $slide->description) !!}</div>
                    <input type="hidden" name="description" id="hiddenDescription">
                </div>

                <div class="panel-layout" style="display:none;">
                    <label class="form-label">Layout</label>
                    <div class="flex gap-3">
                        @foreach (['left' => 'Gambar Kiri, Teks Kanan', 'center' => 'Tengah', 'right' => 'Teks Kiri, Gambar Kanan'] as $val => $lbl)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="layout" value="{{ $val }}"
                                    {{ old('layout', $slide->layout) === $val ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $lbl }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="panel-layout-center">
                    <input type="hidden" name="layout" value="center" id="layout_center_hidden">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Warna Background</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="bg_color"
                                value="{{ old('bg_color', $slide->bg_color ?? '#ffffff') }}"
                                class="w-10 h-10 rounded border border-gray-200 cursor-pointer">
                            <input type="text" id="bg_color_text"
                                value="{{ old('bg_color', $slide->bg_color ?? '#ffffff') }}" class="form-input"
                                style="width:140px;">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Urutan</label>
                        <input type="number" name="order" min="0" value="{{ old('order', $slide->order) }}"
                            class="form-input" required>
                    </div>
                </div>
            </div>

            {{-- Gambar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4" id="panel-images">
                <h2 class="text-base font-semibold text-gray-800">3. Media</h2>

                {{-- Toggle untuk text_carousel: Gambar atau Video --}}
                <div id="carouselMediaToggle" class="flex gap-4 mb-3 hidden">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="carousel_media_type" value="images" checked onchange="toggleCarouselMediaType('images')">
                        <span class="text-sm text-gray-700">Gambar</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="carousel_media_type" value="videos" onchange="toggleCarouselMediaType('videos')">
                        <span class="text-sm text-gray-700">Video</span>
                    </label>
                </div>

                {{-- Image Sections --}}
                <div id="imageSections">
                    @if ($slide->images && count($slide->images) > 0)
                        <div class="mb-4">
                            <label class="form-label">Gambar Upload yang sudah ada</label>
                            <div id="existingImagesArea" class="flex flex-wrap gap-2 mb-3">
                                @foreach ($slide->images as $idx => $imgPath)
                                    <div class="existing-img-wrap" id="existing-wrap-{{ $idx }}" data-original-index="{{ $idx }}">
                                        <img src="{{ asset('storage/' . $imgPath) }}" alt="">
                                        <input type="hidden" name="existing_images[{{ $idx }}]" value="{{ $imgPath }}"
                                            id="existing-input-{{ $idx }}">
                                        <input type="hidden" name="deleted_images[]" id="deleted-input-{{ $idx }}" value="">
                                        <button type="button" class="remove-existing"
                                            onclick="removeExisting({{ $idx }})">✕</button>
                                    </div>
                                @endforeach
                            </div>
                            <div id="existingInfoPopupRows" class="space-y-2">
                                <label class="form-label">Keterangan Info Popup (gambar upload)</label>
                                @foreach ($slide->images as $idx => $imgPath)
                                    <div class="info-popup-row" id="existing-popup-row-{{ $idx }}">
                                        <label class="form-label" style="margin-bottom:4px;">Gambar {{ $idx + 1 }}</label>
                                        <input type="text" name="info_popup_images[{{ $idx }}]" class="form-input"
                                            placeholder="Keterangan gambar {{ $idx + 1 }}..."
                                            value="{{ old("info_popup_images.$idx", $slide->info_popup[$idx] ?? ($slide->info_popup[(string) $idx] ?? '')) }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="image_method" value="upload" checked onchange="toggleImageMethod('upload')">
                            <span class="text-sm text-gray-700">Upload File</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="image_method" value="url" onchange="toggleImageMethod('url')">
                            <span class="text-sm text-gray-700">URL</span>
                        </label>
                    </div>

                    <div id="image-upload-section">
                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-500">Tambah gambar baru (upload)</span>
                            <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="imageInput"
                                onchange="previewNewImages(this)">
                        </label>
                    </div>

                    <div id="image-url-section" class="hidden">
                        <div id="image-url-list" class="space-y-2 mb-3">
                            @if($slide->image_urls && count($slide->image_urls) > 0)
                                @foreach($slide->image_urls as $idx => $imgUrl)
                                <div class="image-url-entry flex gap-2 items-start" data-index="{{ $idx }}">
                                    <a href="{{ $imgUrl }}" target="_blank" class="px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0" title="Buka link">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                    <input type="text" name="image_urls[{{ $idx }}]" class="form-input flex-1"
                                        placeholder="https://example.com/image.jpg atau link Google Drive"
                                        value="{{ $imgUrl }}" data-index="{{ $idx }}">
                                    <button type="button" onclick="removeImageUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            @else
                            <div class="image-url-entry flex gap-2 items-start" data-index="0">
                                <a href="#" target="_blank" class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0 opacity-30 cursor-not-allowed" title="Buka link" onclick="return false;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                <input type="text" name="image_urls[0]" class="form-input flex-1"
                                    placeholder="https://example.com/image.jpg atau link Google Drive" data-index="0" oninput="updateUrlLink(this)">
                                <button type="button" onclick="removeImageUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            @endif
                        </div>
                        <button type="button" onclick="addImageUrlEntry()"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah URL Gambar
                        </button>
                    </div>

                    <div id="newUrlPreviewArea" class="flex flex-wrap gap-3 mt-3 hidden"></div>

                    <div id="infoPopupUploadImageArea" class="mt-4 hidden" style="display: none;">
                        <label class="form-label">Keterangan Info Popup (gambar upload)</label>
                        <div id="infoPopupUploadRows" class="space-y-2">
                        </div>
                    </div>

                    <div id="infoPopupUrlImageArea" class="mt-4 hidden" style="display: none;">
                        <label class="form-label">Keterangan Info Popup (gambar URL)</label>
                        <div id="infoPopupUrlRows" class="space-y-2">
                        </div>
                    </div>
                </div>

                {{-- Preview area for uploaded images (outside imageSections) --}}
                <div id="newImagePreviewArea" class="flex flex-wrap gap-3 mt-3 hidden"></div>

                <script>
                // Initialize on page load
                (function initImageMethod() {
                    var uploadRadio = document.querySelector('input[name="image_method"][value="upload"]');
                    var urlRadio = document.querySelector('input[name="image_method"][value="url"]');
                    var uploadSection = document.getElementById('image-upload-section');
                    var urlSection = document.getElementById('image-url-section');
                    var uploadCaption = document.getElementById('infoPopupUploadImageArea');
                    var urlCaption = document.getElementById('infoPopupUrlImageArea');
                    var uploadPreviewArea = document.getElementById('newImagePreviewArea');
                    var urlPreviewArea = document.getElementById('newUrlPreviewArea');

                    if (urlRadio && urlRadio.checked) {
                        // URL method selected
                        uploadSection.classList.add('hidden');
                        urlSection.classList.remove('hidden');
                        uploadCaption.classList.add('hidden');
                        uploadCaption.style.display = 'none';
                        urlCaption.classList.remove('hidden');
                        urlCaption.style.display = 'block';
                        uploadPreviewArea.classList.add('hidden');
                        urlPreviewArea.classList.remove('hidden');
                        urlPreviewArea.style.display = 'flex';
                        updateUrlImagePreviews();
                    } else {
                        // Upload method selected (default)
                        uploadSection.classList.remove('hidden');
                        urlSection.classList.add('hidden');
                        uploadCaption.classList.remove('hidden');
                        uploadCaption.style.display = 'block';
                        urlCaption.classList.add('hidden');
                        urlCaption.style.display = 'none';
                        uploadPreviewArea.classList.remove('hidden');
                        uploadPreviewArea.style.display = 'flex';
                        urlPreviewArea.classList.add('hidden');
                        urlPreviewArea.style.display = 'none';
                    }
                })();

                document.querySelectorAll('input[name="image_method"]').forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        var uploadSection = document.getElementById('image-upload-section');
                        var urlSection = document.getElementById('image-url-section');
                        var uploadCaption = document.getElementById('infoPopupUploadImageArea');
                        var urlCaption = document.getElementById('infoPopupUrlImageArea');
                        var uploadPreviewArea = document.getElementById('newImagePreviewArea');
                        var urlPreviewArea = document.getElementById('newUrlPreviewArea');

                        if (this.value === 'url') {
                            uploadSection.classList.add('hidden');
                            urlSection.classList.remove('hidden');
                            uploadCaption.classList.add('hidden');
                            uploadCaption.style.display = 'none';
                            urlCaption.classList.remove('hidden');
                            urlCaption.style.display = 'block';
                            uploadPreviewArea.classList.add('hidden');
                            urlPreviewArea.classList.remove('hidden');
                            urlPreviewArea.style.display = 'flex';
                            updateUrlImagePreviews();
                        } else {
                            uploadSection.classList.remove('hidden');
                            urlSection.classList.add('hidden');
                            uploadCaption.classList.remove('hidden');
                            uploadCaption.style.display = 'block';
                            urlCaption.classList.add('hidden');
                            urlCaption.style.display = 'none';
                            uploadPreviewArea.classList.remove('hidden');
                            uploadPreviewArea.style.display = 'flex';
                            urlPreviewArea.classList.add('hidden');
                            urlPreviewArea.style.display = 'none';
                        }
                    });
                });
                </script>

                {{-- Video Sections (for text_carousel) --}}
                <div id="videoSections" class="hidden">
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="carousel_video_method" value="url" checked onchange="toggleCarouselVideoMethod('url')">
                            <span class="text-sm text-gray-700">URL</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="carousel_video_method" value="upload" onchange="toggleCarouselVideoMethod('upload')">
                            <span class="text-sm text-gray-700">Upload File</span>
                        </label>
                    </div>

                    <div id="carousel-video-url-section">
                        <div id="carousel-video-url-list" class="space-y-2 mb-3">
                            @if(!empty($slide->carousel_video_urls))
                                @foreach($slide->carousel_video_urls as $index => $videoUrl)
                                    <div class="carousel-video-url-entry flex gap-2 items-start" data-index="{{ $index }}">
                                        <input type="text" name="carousel_video_urls[]" class="form-input flex-1"
                                            placeholder="https://youtube.com/watch?v=... atau link Google Drive" data-index="{{ $index }}" data-caption="{{ $slide->info_popup['carousel_videos']['url_' . $index] ?? '' }}" value="{{ $videoUrl }}" oninput="updateCarouselUrlCaption(this)">
                                        <button type="button" onclick="removeCarouselVideoUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="carousel-video-url-entry flex gap-2 items-start" data-index="0">
                                    <input type="text" name="carousel_video_urls[]" class="form-input flex-1"
                                        placeholder="https://youtube.com/watch?v=... atau link Google Drive" data-index="0" data-caption="" oninput="updateCarouselUrlCaption(this)">
                                    <button type="button" onclick="removeCarouselVideoUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addCarouselVideoUrlEntry()"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah URL Video
                        </button>
                    </div>

                    <div id="carouselVideoUrlPreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

                    <div id="carousel-video-upload-section" class="hidden">
                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span class="text-sm text-gray-500">Klik untuk pilih video (bisa lebih dari 1, .mp4, .webm)</span>
                            <input type="file" name="carousel_videos[]" multiple accept="video/*" class="hidden" id="carouselVideoInput"
                                onchange="previewCarouselVideos(this)">
                        </label>
                        <div id="carouselVideoPreviewArea" class="flex flex-wrap gap-3 mt-3"></div>
                    </div>

                    <div id="infoPopupCarouselVideoArea">
                        <label class="form-label mt-2">Keterangan Info Popup per Video <span
                                class="text-gray-400 text-xs">(klik tombol ? akan menampilkan teks ini)</span></label>
                        <div id="carouselVideoInfoPopupRows" class="space-y-2">
                            <p class="text-xs text-gray-400 italic" id="noCarouselVideosHint">Tambah video dulu untuk mengisi keterangan popup.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Video --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4" id="panel-video">
                <h2 class="text-base font-semibold text-gray-800">4. Video</h2>

                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="url" {{ (!$slide->video_file) ? 'checked' : '' }} onchange="toggleVideoMethod('url')">
                        <span class="text-sm text-gray-700">URL</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="upload" {{ ($slide->video_file) ? 'checked' : '' }} onchange="toggleVideoMethod('upload')">
                        <span class="text-sm text-gray-700">Upload File</span>
                    </label>
                </div>

                <input type="hidden" name="delete_existing_video" id="deleteExistingVideo" value="0">
                <input type="hidden" name="clear_existing_url" id="clearExistingUrl" value="0">

                <div id="video-url-section" class="{{ ($slide->video_file) ? 'hidden' : '' }}">
                    @if ($slide->video_url)
                        <div class="mb-3">
                            <label class="form-label">Video URL yang sudah ada</label>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600 flex-1 truncate">{{ $slide->video_url }}</span>
                                <a href="{{ $slide->video_url }}" target="_blank" class="text-blue-600 hover:underline text-sm">Lihat</a>
                                <button type="button" @click="deleteUrlModal = true" class="px-3 py-1 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-2 items-start">
                        <input type="text" name="video_url" class="form-input flex-1"
                            placeholder="https://youtube.com/watch?v=..., Google Drive, atau URL video lainnya" value="{{ old('video_url', $slide->video_url) }}" oninput="previewVideoUrl(this)">
                        <div class="url-preview-placeholder w-24 h-16 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <span class="text-xs text-gray-400">Preview</span>
                        </div>
                    </div>
                </div>

                <div id="video-upload-section" class="{{ (!$slide->video_file) ? 'hidden' : '' }}">
                    @if ($slide->video_file)
                        <div id="existingVideoInfo" class="mb-3">
                            <label class="form-label">Video Upload yang sudah ada</label>
                            <div class="flex flex-col gap-3 p-3 bg-gray-50 rounded-lg">
                                <video class="w-full max-w-md rounded" controls>
                                    <source src="{{ asset('storage/' . $slide->video_file) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 truncate">{{ basename($slide->video_file) }}</p>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $slide->video_file) }}" target="_blank" class="text-blue-600 hover:underline text-sm">Buka</a>
                                        <button type="button" @click="deleteVideoModal = true" class="px-3 py-1 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <label
                        class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-sm text-gray-500">Klik untuk pilih video (.mp4, .webm)</span>
                        <input type="file" name="video_file" accept="video/*" class="hidden" id="videoInput"
                            onchange="previewVideoFile(this)">
                    </label>
                    <div id="videoFilePreview" class="mt-3 hidden">
                        <video id="video-preview-player" controls class="w-full max-w-md rounded-lg"></video>
                        <p id="video-file-name" class="text-sm text-gray-500 mt-2"></p>
                    </div>
                </div>

                <div id="infoPopupVideoSection">
                    <label class="form-label">Keterangan Info Popup Video</label>
                    <input type="text" name="info_popup_video" id="infoPopupVideoInput" class="form-input"
                        value="{{ old('info_popup_video', $slide->info_popup['video'] ?? '') }}"
                        placeholder="Keterangan video...">
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 mt-4">
                <a href="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors shadow-sm">
                    Perbarui Slide
                </button>
            </div>

            {{-- Delete Video Modal --}}
            <div x-show="deleteVideoModal" x-cloak class="fixed inset-0 flex items-center justify-center p-4"
                style="z-index: 9999;" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteVideoModal = false"
                    style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100">
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">Hapus Video Upload</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Apakah Anda yakin ingin menghapus video upload ini?
                            </p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <button type="button" @click="deleteVideoModal = false"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="button" @click="deleteVideoModal = false; document.getElementById('deleteExistingVideo').value = '1'; document.getElementById('existingVideoInfo').style.display = 'none'; document.getElementById('infoPopupVideoInput').value = '';"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Clear URL Modal --}}
            <div x-show="deleteUrlModal" x-cloak class="fixed inset-0 flex items-center justify-center p-4"
                style="z-index: 9999;" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteUrlModal = false"
                    style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100">
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">Hapus URL Video</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Apakah Anda yakin ingin menghapus URL video ini?
                            </p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <button type="button" @click="deleteUrlModal = false"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="button" @click="deleteUrlModal = false; document.getElementById('clearExistingUrl').value = '1'; var urlSection = document.getElementById('video-url-section'); var urlDiv = urlSection.querySelector('.mb-3'); if(urlDiv) urlDiv.remove(); document.getElementById('infoPopupVideoInput').value = '';"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://richtexteditor.com/richtexteditor/rte.js"></script>
    <script type="text/javascript" src="https://richtexteditor.com/richtexteditor/plugins/all_plugins.js"></script>
    <script>
        (function() {
            // Existing info_popup data for URL images (indices start after uploaded images)
            var existingUploadedCount = {{ $slide->images ? count($slide->images) : 0 }};
            var existingInfoPopup = {!! json_encode($slide->info_popup ?? []) !!};
            @if($slide->image_urls && count($slide->image_urls) > 0)
                @foreach($slide->image_urls as $idx => $imgUrl)
                    var urlInput{{ $idx }} = document.querySelector('#image-url-list input[name="image_urls[{{ $idx }}]"]');
                    if (urlInput{{ $idx }}) {
                        var captionVal{{ $idx }} = existingInfoPopup[existingUploadedCount + {{ $idx }}] || existingInfoPopup[String(existingUploadedCount + {{ $idx }})] || '';
                        urlInput{{ $idx }}.setAttribute('data-caption', captionVal{{ $idx }});
                    }
                @endforeach
            @endif

            // Existing carousel video files (stored in video_file as JSON array)
            @php
                $existingCarouselVideos = [];
                $carouselVideoOrder = [];
                if ($slide->video_file && $slide->slide_type === 'text_carousel') {
                    $vf = $slide->video_file;
                    if (is_array($vf)) {
                        $existingCarouselVideos = $vf;
                    } elseif (is_string($vf) && str_starts_with($vf, '[')) {
                        $decoded = json_decode($vf, true);
                        $existingCarouselVideos = is_array($decoded) ? $decoded : [];
                    }
                }
                if (!empty($slide->info_popup['carousel_video_order'])) {
                    $carouselVideoOrder = $slide->info_popup['carousel_video_order'];
                }
            @endphp
            var existingCarouselVideos = {!! json_encode($existingCarouselVideos) !!};
            var carouselVideoOrder = {!! json_encode($carouselVideoOrder) !!};

            var typeConfig = {
                hero: {
                    showImages: true,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: false
                },
                text: {
                    showImages: false,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: false
                },
                carousel: {
                    showImages: true,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: false
                },
                video: {
                    showImages: false,
                    showVideo: true,
                    showCarouselVideo: false,
                    showLayout: false
                },
                text_carousel: {
                    showImages: true,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: true
                },
            };

            window.selectType = function(type) {
                document.getElementById('slide_type_input').value = type;
                document.querySelectorAll('.slide-type-card').forEach(function(c) {
                    c.classList.remove('active');
                });
                var card = document.querySelector('.slide-type-card[data-type="' + type + '"]');
                if (card) card.classList.add('active');

                var cfg = typeConfig[type];
                document.getElementById('panel-images').style.display = cfg.showImages ? 'block' : 'none';
                document.getElementById('panel-video').style.display = cfg.showVideo ? 'block' : 'none';
                document.querySelectorAll('.panel-layout').forEach(function(el) {
                    el.style.display = cfg.showLayout ? 'block' : 'none';
                });
                var hiddenLayout = document.getElementById('layout_center_hidden');
                if (hiddenLayout) hiddenLayout.disabled = cfg.showLayout;

                // Show/hide image/video sections based on type
                var imageSections = document.getElementById('imageSections');
                var videoSections = document.getElementById('videoSections');
                var carouselToggle = document.getElementById('carouselMediaToggle');

                if (type === 'text_carousel') {
                    // Show toggle and check for existing video URLs to decide default
                    var hasExistingVideoUrls = document.querySelectorAll('#carousel-video-url-list input[name="carousel_video_urls[]"]').length > 0 &&
                        Array.from(document.querySelectorAll('#carousel-video-url-list input[name="carousel_video_urls[]"]')).some(function(input) {
                            return input.value && input.value.trim() !== '';
                        });

                    // Also check for existing uploaded videos
                    var hasExistingUploadedVideos = (typeof existingCarouselVideos !== 'undefined' && existingCarouselVideos.length > 0);

                    if (carouselToggle) carouselToggle.classList.remove('hidden');

                    if (hasExistingVideoUrls || hasExistingUploadedVideos) {
                        // Use videos mode if there are existing video URLs or uploads
                        imageSections.classList.add('hidden');
                        videoSections.classList.remove('hidden');
                        var vidRadio = carouselToggle ? carouselToggle.querySelector('input[value="videos"]') : null;
                        if (vidRadio) vidRadio.checked = true;
                        toggleCarouselMediaType('videos');

                        // If there are uploaded videos but no URLs, default to upload method
                        if (hasExistingUploadedVideos && !hasExistingVideoUrls) {
                            var uploadRadio = document.querySelector('input[name="carousel_video_method"][value="upload"]');
                            if (uploadRadio) {
                                uploadRadio.checked = true;
                                toggleCarouselVideoMethod('upload');
                            }
                        }
                    } else {
                        // Default to images
                        if (imageSections) imageSections.classList.remove('hidden');
                        if (videoSections) videoSections.classList.add('hidden');
                        var imgRadio = carouselToggle ? carouselToggle.querySelector('input[value="images"]') : null;
                        if (imgRadio) imgRadio.checked = true;
                        toggleCarouselMediaType('images');
                    }
                } else {
                    // Hide toggle, show images by default
                    if (carouselToggle) carouselToggle.classList.add('hidden');
                    if (imageSections) imageSections.classList.remove('hidden');
                    if (videoSections) videoSections.classList.add('hidden');
                }

                // Clear video data when switching to non-video type
                if (!cfg.showVideo) {
                    document.getElementById('deleteExistingVideo').value = '1';
                    document.getElementById('clearExistingUrl').value = '1';
                    var existingVideoInfo = document.getElementById('existingVideoInfo');
                    if (existingVideoInfo) existingVideoInfo.style.display = 'none';
                } else {
                    document.getElementById('deleteExistingVideo').value = '0';
                    document.getElementById('clearExistingUrl').value = '0';
                }
            };

            window.toggleCarouselMediaType = function(type) {
                var isImages = type === 'images';
                var imageSections = document.getElementById('imageSections');
                var videoSections = document.getElementById('videoSections');

                // Update radio button checked state
                var radioImages = document.querySelector('input[name="carousel_media_type"][value="images"]');
                var radioVideos = document.querySelector('input[name="carousel_media_type"][value="videos"]');
                if (radioImages) radioImages.checked = isImages;
                if (radioVideos) radioVideos.checked = !isImages;

                if (isImages) {
                    imageSections.classList.remove('hidden');
                    videoSections.classList.add('hidden');
                } else {
                    imageSections.classList.add('hidden');
                    videoSections.classList.remove('hidden');
                }
            };

            window.toggleImageMethod = function(method) {
                var uploadSection = document.getElementById('image-upload-section');
                var urlSection = document.getElementById('image-url-section');
                var uploadCaptionArea = document.getElementById('infoPopupUploadImageArea');
                var urlCaptionArea = document.getElementById('infoPopupUrlImageArea');
                if (method === 'url') {
                    uploadSection.classList.add('hidden');
                    urlSection.classList.remove('hidden');
                    if (uploadCaptionArea) uploadCaptionArea.classList.add('hidden');
                    if (urlCaptionArea) urlCaptionArea.classList.remove('hidden');
                    updateUrlImagePreviews();
                } else {
                    uploadSection.classList.remove('hidden');
                    urlSection.classList.add('hidden');
                    if (uploadCaptionArea) uploadCaptionArea.classList.remove('hidden');
                    if (urlCaptionArea) urlCaptionArea.classList.add('hidden');
                }
            };

            window.toggleVideoMethod = function(method) {
                var uploadSection = document.getElementById('video-upload-section');
                var urlSection = document.getElementById('video-url-section');
                if (method === 'url') {
                    uploadSection.classList.add('hidden');
                    urlSection.classList.remove('hidden');
                } else {
                    uploadSection.classList.remove('hidden');
                    urlSection.classList.add('hidden');
                }
            };

            window.previewVideoUrl = function(input) {
                var preview = input.closest('#video-url-section').querySelector('.url-preview-placeholder');
                var url = input.value.trim();

                if (!url) {
                    preview.innerHTML = '<span class="text-xs text-gray-400">Preview</span>';
                    return;
                }

                var youtubeId = getYouTubeId(url);
                if (youtubeId) {
                    preview.innerHTML = '<img src="https://img.youtube.com/vi/' + youtubeId + '/1.jpg" class="w-full h-full object-cover rounded-lg">';
                } else if (url.endsWith('.mp4') || url.endsWith('.webm') || url.endsWith('.ogg')) {
                    preview.innerHTML = '<video src="' + url + '" class="w-full h-full object-cover rounded-lg"></video>';
                } else if (url.includes('drive.google.com')) {
                    var gdThumb = convertGoogleDriveUrl(url);
                    if (gdThumb) {
                        preview.innerHTML = '<img src="' + gdThumb + '" class="w-full h-full object-cover rounded-lg" onerror="this.parentElement.innerHTML=\'<div class=\\\'flex flex-col items-center justify-center w-full h-full\\\'><svg class=\\\'w-5 h-5 text-blue-500\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z\\\'/></svg><span class=\\\'text-xs text-blue-500 mt-1\\\'>Google Drive</span></div>\';">';
                    } else {
                        preview.innerHTML = '<div class="flex flex-col items-center justify-center w-full h-full">' +
                            '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' +
                            '<span class="text-xs text-blue-500 mt-1">Google Drive</span></div>';
                    }
                } else if (url.startsWith('http://') || url.startsWith('https://')) {
                    preview.innerHTML = '<div class="flex flex-col items-center justify-center w-full h-full">' +
                        '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' +
                        '<span class="text-xs text-gray-500 mt-1">Video URL</span></div>';
                } else {
                    preview.innerHTML = '<span class="text-xs text-gray-400">Preview</span>';
                }
            };

            window.previewVideoFile = function(input) {
                var previewArea = document.getElementById('videoFilePreview');
                var player = document.getElementById('video-preview-player');
                var fileName = document.getElementById('video-file-name');
                var file = input.files[0];

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        player.src = e.target.result;
                        fileName.textContent = file.name;
                        previewArea.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            };

            window.previewImageUrl = function(input) {
                updateUrlImagePreviews();
            };

            window.removeImageUrlEntry = function(btn) {
                var entry = btn.closest('.image-url-entry');
                if (!entry) return;

                var entries = document.querySelectorAll('#image-url-list .image-url-entry');
                if (entries.length > 1) {
                    entry.remove();
                } else {
                    var input = entry.querySelector('input[name^="image_urls"]');
                    if (input) input.value = '';
                }
                updateUrlImagePreviews();
            };

            window.addImageUrlEntry = function() {
                var list = document.getElementById('image-url-list');
                var entries = list.querySelectorAll('.image-url-entry');
                var newIndex = entries.length;

                var entry = document.createElement('div');
                entry.className = 'image-url-entry flex gap-2 items-start';
                entry.setAttribute('data-index', newIndex);
                entry.innerHTML =
                    '<a href="#" target="_blank" class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0 opacity-30 cursor-not-allowed" title="Buka link" onclick="return false;">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>' +
                    '<input type="text" name="image_urls[' + newIndex + ']" class="form-input flex-1" ' +
                    'placeholder="https://example.com/image.jpg atau link Google Drive" data-index="' + newIndex + '" oninput="updateUrlLink(this)">' +
                    '<button type="button" onclick="removeImageUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
                list.appendChild(entry);

                // Also add the corresponding caption field
                updateUrlImagePreviews();
            };

            window.updateUrlLink = function(input) {
                var entry = input.closest('.image-url-entry');
                var linkBtn = entry.querySelector('.url-link-btn');
                var url = input.value.trim();

                if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                    linkBtn.href = url;
                    linkBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                    linkBtn.onclick = null;
                } else {
                    linkBtn.href = '#';
                    linkBtn.classList.add('opacity-30', 'cursor-not-allowed');
                    linkBtn.onclick = function() { return false; };
                }
            };

            // Unified preview function for URL images and uploads
            function updateUrlImagePreviews() {
                var uploadPreviewArea = document.getElementById('newImagePreviewArea');
                var urlPreviewArea = document.getElementById('newUrlPreviewArea');
                var urlCaptionArea = document.getElementById('infoPopupUrlImageArea');
                var urlCaptionRows = document.getElementById('infoPopupUrlRows');
                var uploadCaptionArea = document.getElementById('infoPopupUploadImageArea');
                var uploadCaptionRows = document.getElementById('infoPopupUploadRows');

                // Get all URL inputs
                var urlInputs = document.querySelectorAll('#image-url-list input[name^="image_urls"]');
                var urlImages = [];

                urlInputs.forEach(function(input, inputIdx) {
                    var url = input.value.trim();
                    var entry = input.closest('.image-url-entry');
                    var linkBtn = entry ? entry.querySelector('.url-link-btn') : null;
                    var caption = input.getAttribute('data-caption') || '';

                    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                        if (linkBtn) {
                            linkBtn.href = url;
                            linkBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                            linkBtn.onclick = null;
                        }
                        var displayUrl = url;
                        if (url.includes('drive.google.com') && url.includes('/file/d/')) {
                            var fileId = url.match(/\/file\/d\/([^\/]+)/);
                            if (fileId) {
                                displayUrl = 'https://lh3.googleusercontent.com/d/' + fileId[1];
                            }
                        }
                        urlImages.push({ url: displayUrl, originalUrl: url, hasUrl: true, caption: caption });
                    } else {
                        urlImages.push({ url: '', originalUrl: '', hasUrl: false, caption: caption });
                        if (linkBtn) {
                            linkBtn.href = '#';
                            linkBtn.classList.add('opacity-30', 'cursor-not-allowed');
                            linkBtn.onclick = function() { return false; };
                        }
                    }
                });

                var existingUploadedCount = getRemainingExistingCount();

                // Clear both preview areas
                uploadPreviewArea.innerHTML = '';
                urlPreviewArea.innerHTML = '';

                // Show/hide URL preview area based on URL images
                if (urlImages.some(function(img) { return img.hasUrl; })) {
                    urlPreviewArea.classList.remove('hidden');
                } else {
                    urlPreviewArea.classList.add('hidden');
                }

                // Show/hide upload preview area based on uploaded files
                if (selectedNewImageFiles.length > 0) {
                    uploadPreviewArea.classList.remove('hidden');
                } else {
                    uploadPreviewArea.classList.add('hidden');
                }

                // Show/hide URL caption section
                if (urlImages.length === 0) {
                    urlCaptionArea.classList.add('hidden');
                } else {
                    urlCaptionArea.classList.remove('hidden');
                }

                // Render URL image previews
                urlImages.forEach(function(img, idx) {
                    if (img.hasUrl) {
                        var wrap = document.createElement('div');
                        wrap.className = 'img-preview-wrap';
                        var isGoogleDrive = img.originalUrl.includes('drive.google.com');

                        if (isGoogleDrive) {
                            wrap.innerHTML = '<div class="flex flex-col items-center justify-center" style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;">' +
                                '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' +
                                '<a href="' + img.originalUrl + '" target="_blank" class="text-xs text-blue-500 hover:text-blue-700 mt-1">Lihat</a></div>' +
                                '<button type="button" class="remove-img" onclick="removeUrlImage(' + idx + ')">✕</button>';
                        } else {
                            wrap.innerHTML = '<img src="' + img.url + '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">' +
                                '<div class="flex items-center justify-center" style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:none;"><span class="text-xs text-gray-400">Error</span></div>' +
                                '<button type="button" class="remove-img" onclick="removeUrlImage(' + idx + ')">✕</button>';
                        }
                        urlPreviewArea.appendChild(wrap);
                    }
                });

                // Render new uploaded image previews
                selectedNewImageFiles.forEach(function(file, idx) {
                    var reader = new FileReader();
                    (function(index, fileReader) {
                        fileReader.onload = function(e) {
                            var wrap = document.createElement('div');
                            wrap.className = 'img-preview-wrap';
                            wrap.innerHTML = '<img src="' + e.target.result + '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">' +
                                '<button type="button" class="remove-img" onclick="removePreviewImage(' + index + ')">✕</button>';
                            uploadPreviewArea.appendChild(wrap);
                        };
                    })(idx, reader);
                    reader.readAsDataURL(file);
                });

                // Show/hide upload caption section
                if (selectedNewImageFiles.length === 0) {
                    uploadCaptionArea.classList.add('hidden');
                } else {
                    uploadCaptionArea.classList.remove('hidden');
                }

                // Render caption fields for URL images
                urlCaptionRows.innerHTML = '';
                urlImages.forEach(function(img, idx) {
                    var captionIndex = existingUploadedCount + idx;
                    var captionValue = img.caption || '';
                    var row = document.createElement('div');
                    row.className = 'info-popup-row';
                    var inputEl = document.createElement('input');
                    inputEl.type = 'text';
                    inputEl.name = 'info_popup_images[' + captionIndex + ']';
                    inputEl.className = 'form-input';
                    inputEl.placeholder = 'Keterangan gambar URL ' + (idx + 1) + '...';
                    inputEl.value = captionValue;
                    // Save caption to URL input's data attribute when user types
                    inputEl.addEventListener('input', function() {
                        var urlInputs = document.querySelectorAll('#image-url-list input[name^="image_urls"]');
                        if (urlInputs[idx]) {
                            urlInputs[idx].setAttribute('data-caption', this.value);
                        }
                    });
                    var labelEl = document.createElement('label');
                    labelEl.className = 'form-label';
                    labelEl.style.marginBottom = '4px';
                    labelEl.textContent = 'Gambar URL ' + (idx + 1);
                    row.appendChild(labelEl);
                    row.appendChild(inputEl);
                    urlCaptionRows.appendChild(row);
                });

                // Render caption fields for new uploaded images
                uploadCaptionRows.innerHTML = '';
                selectedNewImageFiles.forEach(function(file, idx) {
                    var captionIndex = idx; // New uploads use sequential indices starting from 0
                    var row = document.createElement('div');
                    row.className = 'info-popup-row';
                    row.innerHTML = '<label class="form-label" style="margin-bottom:4px;">Gambar Upload ' + (idx + 1) + '</label>' +
                        '<input type="text" name="info_popup_new_images[' + captionIndex + ']" class="form-input" placeholder="Keterangan gambar upload ' + (idx + 1) + '...">';
                    uploadCaptionRows.appendChild(row);
                });
            }

            window.removeUrlImage = function(idx) {
                var entries = document.querySelectorAll('#image-url-list .image-url-entry');
                if (entries.length > 1) {
                    // Remove the entire entry
                    if (entries[idx]) {
                        entries[idx].remove();
                    }
                } else {
                    // Just clear the URL input if it's the only entry
                    var inputs = document.querySelectorAll('#image-url-list input[name^="image_urls"]');
                    if (inputs[0]) {
                        inputs[0].value = '';
                    }
                }
                updateUrlImagePreviews();
            };

            var selectedNewImageFiles = [];

            window.previewNewImages = function(input) {
                var files = Array.from(input.files);
                var existingCount = {{ $slide->images ? count($slide->images) : 0 }};

                if (files.length === 0) return;

                files.forEach(function(file) {
                    selectedNewImageFiles.push(file);
                });

                renderNewImagePreviews();
            };

            function getRemainingExistingCount() {
                var existingInputs = document.querySelectorAll('#existingImagesArea input[name^="existing_images"]');
                return existingInputs.length;
            }

            function renderNewImagePreviews() {
                updateUrlImagePreviews();
            }

            window.removePreviewImage = function(idx) {
                selectedNewImageFiles.splice(idx, 1);
                renderNewImagePreviews();
            };

            window.removeExisting = function(idx) {
                var wrap = document.getElementById('existing-wrap-' + idx);
                var input = document.getElementById('existing-input-' + idx);
                var popupRow = document.getElementById('existing-popup-row-' + idx);
                var deletedInput = document.getElementById('deleted-input-' + idx);

                if (wrap) {
                    wrap.style.opacity = '0.5';
                    wrap.style.pointerEvents = 'none';
                }
                if (input) {
                    input.disabled = true;
                    input.name = 'deleted_existing_images[' + idx + ']';
                }
                if (popupRow) {
                    popupRow.style.opacity = '0.5';
                    popupRow.style.pointerEvents = 'none';
                    var inputField = popupRow.querySelector('input');
                    if (inputField) {
                        inputField.disabled = true;
                        inputField.name = 'deleted_info_popup_images[' + idx + ']';
                    }
                }
                if (deletedInput) {
                    deletedInput.value = '1';
                    deletedInput.name = 'deleted_existing_images_marked[' + idx + ']';
                }

                // Re-render new image previews with updated count
                renderNewImagePreviews();
            };

            window.toggleCarouselVideoMethod = function(method) {
                var urlSection = document.getElementById('carousel-video-url-section');
                var uploadSection = document.getElementById('carousel-video-upload-section');
                if (method === 'url') {
                    urlSection.classList.remove('hidden');
                    uploadSection.classList.add('hidden');
                } else {
                    urlSection.classList.add('hidden');
                    uploadSection.classList.remove('hidden');
                }
            };

            window.addCarouselVideoUrlEntry = function() {
                var list = document.getElementById('carousel-video-url-list');
                var entries = list.querySelectorAll('.carousel-video-url-entry');
                var newIndex = entries.length;

                var entry = document.createElement('div');
                entry.className = 'carousel-video-url-entry flex gap-2 items-start';
                entry.setAttribute('data-index', newIndex);
                entry.innerHTML =
                    '<input type="text" name="carousel_video_urls[]" class="form-input flex-1" ' +
                    'placeholder="https://youtube.com/watch?v=... atau link Google Drive" data-index="' + newIndex + '" data-caption="" oninput="updateCarouselUrlCaption(this)">' +
                    '<button type="button" onclick="removeCarouselVideoUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
                list.appendChild(entry);

                // Add to allVideoEntries for tracking
                allVideoEntries.push({
                    type: 'url',
                    data: '',
                    domIndex: newIndex,
                    caption: ''
                });
            };

            window.updateCarouselUrlCaption = function(input) {
                var domIndex = parseInt(input.getAttribute('data-index'));
                // Update URL data only — do NOT set caption from input value
                allVideoEntries.forEach(function(entry) {
                    if (entry.type === 'url' && entry.domIndex === domIndex) {
                        entry.data = input.value;
                        // Caption is managed separately via urlCaptionTracker, not from URL input
                    }
                });
                updateCarouselVideoPreviews();
            };

            window.removeCarouselVideoUrlEntry = function(btn) {
                var entries = document.querySelectorAll('.carousel-video-url-entry');
                if (entries.length > 1) {
                    // Caption is already saved in urlCaptionTracker by updateCarouselUrlCaption
                    btn.closest('.carousel-video-url-entry').remove();
                } else {
                    var inputs = document.querySelectorAll('#carousel-video-url-list input[name="carousel_video_urls[]"]');
                    if (inputs[0]) {
                        inputs[0].value = '';
                        // Caption is preserved in urlCaptionTracker
                    }
                }
                updateCarouselVideoPreviews();
            };

            window.previewCarouselVideoUrl = function(input) {
                updateCarouselVideoPreviews();
            };

            function getYouTubeId(url) {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                var match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }

            function convertGoogleDriveUrl(url) {
                var match = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
                if (match) {
                    return 'https://lh3.googleusercontent.com/d/' + match[1];
                }
                match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                if (match) {
                    return 'https://lh3.googleusercontent.com/d/' + match[1];
                }
                return null;
            }

            function updateCarouselVideoPreviews() {
                var previewArea = document.getElementById('carouselVideoUrlPreviewArea');
                var popupRows = document.getElementById('carouselVideoInfoPopupRows');
                var hint = document.getElementById('noCarouselVideosHint');

                // Sync allVideoEntries with current DOM state for URLs
                var urlInputs = document.querySelectorAll('#carousel-video-url-list input[name="carousel_video_urls[]"]');
                urlInputs.forEach(function(input, idx) {
                    var url = input.value.trim();
                    // Use urlCaptionTracker for caption so it survives re-renders
                    var caption = urlCaptionTracker[idx] || '';

                    // Check if this URL already exists in allVideoEntries
                    var existingEntry = null;
                    allVideoEntries.forEach(function(entry) {
                        if (entry.type === 'url' && entry.domIndex === idx) {
                            existingEntry = entry;
                        }
                    });

                    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                        if (existingEntry) {
                            existingEntry.data = url;
                            existingEntry.caption = caption;
                        } else {
                            // New URL added via DOM
                            allVideoEntries.push({
                                type: 'url',
                                data: url,
                                domIndex: idx,
                                caption: caption
                            });
                        }
                    } else {
                        // URL was cleared, remove from entries but preserve caption in tracker
                        if (existingEntry) {
                            allVideoEntries = allVideoEntries.filter(function(e) {
                                return !(e.type === 'url' && e.domIndex === idx);
                            });
                        }
                    }
                });

                // Clean up: remove URL entries that no longer exist in DOM
                allVideoEntries = allVideoEntries.filter(function(entry) {
                    if (entry.type === 'url') {
                        var entries = document.querySelectorAll('.carousel-video-url-entry');
                        return entry.domIndex < entries.length;
                    }
                    return true;
                });

                // Count valid videos
                var validVideos = allVideoEntries.filter(function(entry) {
                    if (entry.type === 'url') {
                        return entry.data && (entry.data.startsWith('http://') || entry.data.startsWith('https://'));
                    }
                    // Upload is valid if it has data (new upload) or uploadPath (existing upload)
                    return entry.data !== null || entry.uploadPath;
                });

                // Clear preview area
                previewArea.innerHTML = '';

                if (validVideos.length === 0) {
                    if (hint) hint.style.display = '';
                    popupRows.innerHTML = '<p class="text-xs text-gray-400 italic" id="noCarouselVideosHint">Tambah video dulu untuk mengisi keterangan popup.</p>';
                    return;
                }

                if (hint) hint.style.display = 'none';
                popupRows.innerHTML = '';

                // Track sequential indices for caption keys
                var urlRenderIdx = 0;
                var existingUploadIdx = 0;
                var newUploadIdx = 0;

                // Render all videos in order (allVideoEntries is already in chronological order)
                allVideoEntries.forEach(function(video, renderIndex) {
                    // Skip invalid entries
                    if (video.type === 'url' && (!video.data || (!video.data.startsWith('http://') && !video.data.startsWith('https://')))) {
                        return;
                    }
                    if (video.type === 'upload' && !video.data && !video.uploadPath) {
                        return;
                    }

                    var wrap = document.createElement('div');
                    wrap.className = 'img-preview-wrap';
                    var displayPosition = renderIndex + 1;

                    // Generate caption key based on type and sequential index
                    var captionKey;
                    if (video.type === 'url') {
                        captionKey = 'url_' + urlRenderIdx;
                        urlRenderIdx++;
                    } else if (video.uploadPath) {
                        captionKey = 'upload_' + existingUploadIdx;
                        existingUploadIdx++;
                    } else {
                        captionKey = 'newUpload_' + newUploadIdx;
                        newUploadIdx++;
                    }

                    if (video.type === 'url') {
                        // URL video
                        var youtubeId = getYouTubeId(video.data);

                        if (youtubeId) {
                            wrap.innerHTML = '<img src="https://img.youtube.com/vi/' + youtubeId + '/1.jpg" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" class="rounded-lg">' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.domIndex + ')">✕</button>';
                        } else if (video.data.endsWith('.mp4') || video.data.endsWith('.webm') || video.data.endsWith('.ogg')) {
                            wrap.innerHTML = '<video src="' + video.data + '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.domIndex + ')">✕</button>';
                        } else if (video.data.includes('drive.google.com')) {
                            var gdThumb = convertGoogleDriveUrl(video.data);
                            if (gdThumb) {
                                wrap.innerHTML = '<img src="' + gdThumb + '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">' +
                                    '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center" style="display:none;"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.domIndex + ')">✕</button>';
                            } else {
                                wrap.innerHTML = '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.domIndex + ')">✕</button>';
                            }
                        } else {
                            wrap.innerHTML = '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.domIndex + ')">✕</button>';
                        }

                        previewArea.appendChild(wrap);

                        // Add caption input with event listener to save
                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var captionInput = document.createElement('input');
                        captionInput.type = 'text';
                        captionInput.name = 'info_popup_carousel_videos[' + captionKey + ']';
                        captionInput.className = 'form-input';
                        captionInput.placeholder = 'Keterangan video ' + displayPosition + ' (opsional)...';
                        captionInput.value = video.caption || '';
                        captionInput.addEventListener('input', function() {
                            video.caption = this.value;
                            // Update tracker so caption survives re-renders
                            if (video.type === 'url') {
                                urlCaptionTracker[video.domIndex] = this.value;
                            }
                        });
                        var label = document.createElement('label');
                        label.className = 'form-label';
                        label.style.marginBottom = '4px';
                        label.textContent = 'Video URL ' + displayPosition;
                        row.appendChild(label);
                        row.appendChild(captionInput);
                        popupRows.appendChild(row);
                    } else {
                        // Upload video
                        var videoSrc = video.data ? URL.createObjectURL(video.data) : (video.uploadPath ? '{{ asset("storage/") }}/' + video.uploadPath : '');
                        var uploadWrap = document.createElement('div');
                        uploadWrap.className = 'img-preview-wrap';
                        uploadWrap.innerHTML = '<video src="' + videoSrc + '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                            '<button type="button" class="remove-img" onclick="removePreviewVideo(' + video.uploadId + ')">✕</button>';
                        previewArea.appendChild(uploadWrap);

                        // Add caption input with event listener to save
                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var captionInput = document.createElement('input');
                        captionInput.type = 'text';
                        captionInput.name = 'info_popup_carousel_videos[' + captionKey + ']';
                        captionInput.className = 'form-input';
                        captionInput.placeholder = 'Keterangan video ' + displayPosition + ' (opsional)...';
                        captionInput.value = video.caption || '';
                        captionInput.addEventListener('input', function() {
                            video.caption = this.value;
                        });
                        var label = document.createElement('label');
                        label.className = 'form-label';
                        label.style.marginBottom = '4px';
                        label.textContent = 'Video Upload ' + displayPosition;
                        row.appendChild(label);
                        row.appendChild(captionInput);
                        popupRows.appendChild(row);
                    }
                });
            }

            // Single array to track ALL videos in chronological order
            var allVideoEntries = []; // [{type: 'url'|'upload', data: string|file, caption: string, domIndex: number}]
            var uploadCounter = 0; // Counter for unique upload IDs
            var selectedCarouselVideoFiles = []; // Separate array to reliably track new video files

            // Separate tracker for URL captions keyed by domIndex to survive re-renders
            var urlCaptionTracker = {};

            window.removeUrlVideo = function(domIndex) {
                // Remove from allVideoEntries
                allVideoEntries = allVideoEntries.filter(function(entry) {
                    return !(entry.type === 'url' && entry.domIndex === domIndex);
                });

                // Also clear the input in DOM but PRESERVE caption in tracker
                var entries = document.querySelectorAll('.carousel-video-url-entry');
                if (entries[domIndex]) {
                    var inputs = entries[domIndex].querySelectorAll('input[name="carousel_video_urls[]"]');
                    if (inputs.length > 0) {
                        inputs[0].value = '';
                    }
                }
                updateCarouselVideoPreviews();
            };

            window.previewCarouselVideos = function(input) {
                var files = Array.from(input.files);

                if (files.length === 0) return;

                files.forEach(function(file) {
                    uploadCounter++;
                    // Add to reliable file tracker first
                    selectedCarouselVideoFiles.push(file);
                    // Also add to allVideoEntries
                    allVideoEntries.push({
                        type: 'upload',
                        data: file,
                        uploadId: uploadCounter,
                        caption: ''
                    });
                });

                // Clear the input so browser doesn't auto-retain old files
                input.value = '';

                renderNewCarouselVideoPreviews();
            };

            function renderNewCarouselVideoPreviews() {
                updateCarouselVideoPreviews();
            }

            window.removePreviewVideo = function(uploadId) {
                // Find the entry first to get the file reference
                var removedEntry = null;
                allVideoEntries.forEach(function(entry) {
                    if (entry.type === 'upload' && entry.uploadId === uploadId) {
                        removedEntry = entry;
                    }
                });

                // Remove from allVideoEntries by uploadId
                allVideoEntries = allVideoEntries.filter(function(entry) {
                    return !(entry.type === 'upload' && entry.uploadId === uploadId);
                });

                // Remove from selectedCarouselVideoFiles if it's a new upload (has File data)
                if (removedEntry && removedEntry.data instanceof File) {
                    selectedCarouselVideoFiles = selectedCarouselVideoFiles.filter(function(f) {
                        return f !== removedEntry.data;
                    });
                }

                renderNewCarouselVideoPreviews();
            };

            selectType('{{ $slide->slide_type }}');

            // Initialize URL image previews and carousel video previews on load
            setTimeout(function() {
                // Get captions from info_popup (available in both branches)
                var captions = {};
                @if(!empty($slide->info_popup['carousel_videos']))
                    captions = {!! json_encode($slide->info_popup['carousel_videos']) !!};
                @endif

                // If carouselVideoOrder exists, use it to rebuild allVideoEntries in correct order
                if (typeof carouselVideoOrder !== 'undefined' && carouselVideoOrder.length > 0) {
                    // Get all URL inputs and captions from DOM
                    var urlInputs = document.querySelectorAll('#carousel-video-url-list input[name="carousel_video_urls[]"]');

                    // Build entries based on carouselVideoOrder
                    carouselVideoOrder.forEach(function(item, orderIdx) {
                        if (item.type === 'url') {
                            var urlIdx = item.urlIndex;
                            var urlInput = urlInputs[urlIdx];
                            if (urlInput && urlInput.value && urlInput.value.trim() !== '') {
                                var urlCaptionKey = 'url_' + urlIdx;
                                var caption = captions[urlCaptionKey] || '';
                                // Populate urlCaptionTracker so caption survives re-renders
                                urlCaptionTracker[urlIdx] = caption;
                                allVideoEntries.push({
                                    type: 'url',
                                    data: urlInput.value,
                                    domIndex: urlIdx,
                                    caption: caption
                                });
                            }
                        } else if (item.type === 'upload') {
                            var uploadPath = item.uploadPath;
                            if (uploadPath) {
                                uploadCounter++;
                                var uploadIdx = item.uploadIndex;
                                var uploadCaptionKey = 'upload_' + uploadIdx;
                                var caption = captions[uploadCaptionKey] || '';
                                allVideoEntries.push({
                                    type: 'upload',
                                    data: null,
                                    uploadId: uploadCounter,
                                    uploadPath: uploadPath,
                                    caption: caption
                                });
                            }
                        } else if (item.type === 'newUpload') {
                            // This shouldn't happen on page load, but handle it anyway
                            if (typeof existingCarouselVideos !== 'undefined' && existingCarouselVideos.length > 0) {
                                var newUploadIdx = item.newUploadIndex;
                                if (existingCarouselVideos[newUploadIdx]) {
                                    uploadCounter++;
                                    var newUploadCaptionKey = 'newUpload_' + newUploadIdx;
                                    var caption = captions[newUploadCaptionKey] || '';
                                    allVideoEntries.push({
                                        type: 'upload',
                                        data: null,
                                        uploadId: uploadCounter,
                                        uploadPath: existingCarouselVideos[newUploadIdx],
                                        caption: caption
                                    });
                                }
                            }
                        }
                    });
                } else {
                    // Fallback: load URLs from DOM (existing behavior)
                    updateCarouselVideoPreviews();

                    // Add existing uploaded videos to allVideoEntries
                    if (typeof existingCarouselVideos !== 'undefined' && existingCarouselVideos.length > 0) {
                        existingCarouselVideos.forEach(function(videoPath, idx) {
                            var exists = allVideoEntries.some(function(entry) {
                                return entry.type === 'upload' && entry.uploadPath === videoPath;
                            });
                            if (!exists) {
                                uploadCounter++;
                                var caption = captions['upload_' + idx] || '';
                                allVideoEntries.push({
                                    type: 'upload',
                                    data: null,
                                    uploadId: uploadCounter,
                                    uploadPath: videoPath,
                                    caption: caption
                                });
                            }
                        });
                    }
                }

                updateUrlImagePreviews();
                updateCarouselVideoPreviews();

                // Initialize unifiedVideoOrder hidden input from allVideoEntries for form submission
                (function syncUnifiedVideoOrder() {
                    var unifiedOrder = [];
                    var urlIdx = 0;
                    var uploadIdx = 0;
                    var newUploadIdx = 0;
                    allVideoEntries.forEach(function(entry, orderIdx) {
                        if (entry.type === 'url' && entry.data) {
                            unifiedOrder.push({ type: 'url', urlValue: entry.data, urlIndex: urlIdx, order: orderIdx });
                            urlIdx++;
                        } else if (entry.type === 'upload') {
                            if (entry.uploadPath) {
                                unifiedOrder.push({ type: 'upload', uploadPath: entry.uploadPath, uploadIndex: uploadIdx, order: orderIdx });
                                uploadIdx++;
                            } else if (entry.data) {
                                unifiedOrder.push({ type: 'newUpload', newUploadIndex: newUploadIdx, order: orderIdx });
                                newUploadIdx++;
                            }
                        }
                    });
                    var unifiedInput = document.getElementById('unifiedVideoOrder');
                    if (unifiedInput && unifiedOrder.length > 0) {
                        unifiedInput.value = JSON.stringify(unifiedOrder);
                    }
                })();
            }, 100);

            // Initialize video method visibility based on existing data
            var videoMethodRadios = document.querySelectorAll('input[name="video_method"]');
            videoMethodRadios.forEach(function(radio) {
                if (radio.checked) {
                    toggleVideoMethod(radio.value);
                }
            });

            // Initialize carousel video method visibility based on existing data
            var carouselVideoMethodRadios = document.querySelectorAll('input[name="carousel_video_method"]');
            carouselVideoMethodRadios.forEach(function(radio) {
                if (radio.checked) {
                    toggleCarouselVideoMethod(radio.value);
                }
            });

            var bgColorInput = document.querySelector('[name=bg_color]');
            if (bgColorInput) {
                bgColorInput.addEventListener('input', function() {
                    document.getElementById('bg_color_text').value = this.value;
                });
            }

            var editor1 = null;

            function initRTE() {
                if (typeof RichTextEditor === 'undefined') {
                    setTimeout(initRTE, 200);
                    return;
                }
                try {
                    editor1 = new RichTextEditor("#div_editor1", {
                        content_css: "https://richtexteditor.com/richtexteditor/rte_theme_default.css",
                        contentCssUrl: "https://richtexteditor.com/richtexteditor/rte_content.css"
                    });
                } catch (e) {
                    console.error('RTE init error:', e);
                }
            }

            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                initRTE();
            } else {
                window.addEventListener('load', initRTE);
            }

            document.getElementById('slideForm').addEventListener('submit', function(e) {
                if (editor1) {
                    try {
                        document.getElementById('hiddenDescription').value = editor1.getHTMLCode();
                    } catch (err) {
                        try {
                            document.getElementById('hiddenDescription').value = editor1.getHTML();
                        } catch (e2) {
                            console.error('RTE getHTML error:', e2);
                        }
                    }
                }

                var form = document.getElementById('slideForm');

                // Set files on the existing image input
                var imageInput = document.getElementById('imageInput');
                if (imageInput && selectedNewImageFiles.length > 0) {
                    var imageDataTransfer = new DataTransfer();
                    selectedNewImageFiles.forEach(function(file) {
                        imageDataTransfer.items.add(file);
                    });
                    imageInput.files = imageDataTransfer.files;
                    console.log('Set imageInput files:', imageInput.files.length);
                }

                // Set files on the existing carousel video input using reliable file array
                var carouselInput = document.getElementById('carouselVideoInput');
                if (carouselInput && selectedCarouselVideoFiles.length > 0) {
                    var videoDataTransfer = new DataTransfer();
                    selectedCarouselVideoFiles.forEach(function(file) {
                        videoDataTransfer.items.add(file);
                    });
                    carouselInput.files = videoDataTransfer.files;
                }

                // Build unified_video_order and existing_carousel_videos for form submission
                var unifiedOrder = [];
                var existingVideos = [];
                var urlIdx = 0;
                var uploadIdx = 0;
                var newUploadIdx = 0;

                allVideoEntries.forEach(function(entry, idx) {
                    if (entry.type === 'url' && entry.data) {
                        unifiedOrder.push({
                            type: 'url',
                            urlValue: entry.data,
                            urlIndex: urlIdx,
                            order: idx
                        });
                        urlIdx++;
                    } else if (entry.type === 'upload') {
                        if (entry.uploadPath) {
                            // Existing upload from database
                            unifiedOrder.push({
                                type: 'upload',
                                uploadPath: entry.uploadPath,
                                uploadIndex: uploadIdx,
                                order: idx
                            });
                            existingVideos.push(entry.uploadPath);
                            uploadIdx++;
                        } else if (entry.data) {
                            // New upload
                            unifiedOrder.push({
                                type: 'newUpload',
                                newUploadIndex: newUploadIdx,
                                order: idx
                            });
                            newUploadIdx++;
                        }
                    }
                });

                // Set hidden fields
                document.getElementById('unifiedVideoOrder').value = JSON.stringify(unifiedOrder);
                document.getElementById('existingCarouselVideosInput').value = JSON.stringify(existingVideos);
                console.log('Set unifiedVideoOrder:', unifiedOrder.length, 'existingCarouselVideos:', existingVideos.length);
            });
        })();
    </script>
@endpush
