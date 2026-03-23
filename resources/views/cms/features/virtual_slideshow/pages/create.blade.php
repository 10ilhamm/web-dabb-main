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
@section('breadcrumb_active', 'Tambah Slide')

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

        .section-panel {
            display: none;
        }

        .section-panel.active {
            display: block;
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
            display: none;
        }

        #newImagePreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #carouselVideoPreviewArea {
            display: none;
        }

        #urlImagePreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #carouselVideoUrlPreviewArea {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">

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
                <h1 class="text-2xl font-bold text-gray-800">Tambah Slide Baru</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
                @if (isset($page))
                    <p class="text-sm text-blue-600 mt-0.5">Halaman: {{ $page->title }}</p>
                @endif
            </div>
        </div>

        <form
            action="{{ isset($page) ? route('cms.features.slideshow.pages.slides.store', [$feature, $page]) : route('cms.features.slideshow.store', $feature) }}"
            method="POST" enctype="multipart/form-data" id="slideForm"
            data-redirect="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}">
            @csrf

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

            {{-- Step 1: Tipe Slide --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">1. Pilih Tipe Slide</h2>
                <input type="hidden" name="slide_type" id="slide_type_input" value="text">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    <div class="slide-type-card active" data-type="text" onclick="selectType('text')">
                        <div class="icon">📝</div>
                        <div class="label">Teks</div>
                        <div class="desc">Konten teks saja</div>
                    </div>
                    <div class="slide-type-card" data-type="hero" onclick="selectType('hero')">
                        <div class="icon">🌟</div>
                        <div class="label">Hero</div>
                        <div class="desc">Banner pembuka</div>
                    </div>
                    <div class="slide-type-card" data-type="carousel" onclick="selectType('carousel')">
                        <div class="icon">🖼️</div>
                        <div class="label">Carousel</div>
                        <div class="desc">Slideshow gambar</div>
                    </div>
                    <div class="slide-type-card" data-type="video" onclick="selectType('video')">
                        <div class="icon">🎬</div>
                        <div class="label">Video</div>
                        <div class="desc">Embed video</div>
                    </div>
                    <div class="slide-type-card" data-type="text_carousel" onclick="selectType('text_carousel')">
                        <div class="icon">📋</div>
                        <div class="label">Teks + Carousel</div>
                        <div class="desc">Split layout</div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Konten Umum --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">2. Konten</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Judul <span class="text-gray-400 text-xs">(opsional)</span></label>
                        <input type="text" name="title" class="form-input" placeholder="Judul slide..."
                            value="{{ old('title') }}">
                    </div>
                    <div>
                        <label class="form-label">Sub-judul <span class="text-gray-400 text-xs">(opsional)</span></label>
                        <input type="text" name="subtitle" class="form-input" placeholder="Sub-judul..."
                            value="{{ old('subtitle') }}">
                    </div>
                </div>

                <div>
                    <label class="form-label">Deskripsi / Teks Konten <span class="text-gray-400 text-xs">(opsional -
                            gunakan toolbar untuk format)</span></label>
                    <div id="div_editor1" style="min-width:100%;">{!! old('description') !!}</div>
                    <input type="hidden" name="description" id="hiddenDescription">
                </div>

                {{-- Layout --}}
                <div class="panel-layout" style="display:none;">
                    <label class="form-label">Layout</label>
                    <div class="flex gap-3">
                        @foreach (['left' => 'Gambar Kiri, Teks Kanan', 'center' => 'Tengah', 'right' => 'Teks Kiri, Gambar Kanan'] as $val => $lbl)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="layout" value="{{ $val }}"
                                    {{ old('layout', 'center') === $val ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $lbl }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="panel-layout-center">
                    <input type="hidden" name="layout" value="center" id="layout_center_hidden">
                </div>

                {{-- BG Color --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Warna Background Section</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="bg_color" value="{{ old('bg_color', '#ffffff') }}"
                                class="w-10 h-10 rounded border border-gray-200 cursor-pointer">
                            <input type="text" id="bg_color_text" value="{{ old('bg_color', '#ffffff') }}"
                                class="form-input" style="width:140px;"
                                onchange="document.querySelector('[name=bg_color]').value=this.value">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Urutan</label>
                        <input type="number" name="order" min="0" value="{{ old('order', 1) }}"
                            class="form-input" required>
                    </div>
                </div>
            </div>

            {{-- Step 3: Media --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4 section-panel active"
                id="panel-images">
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
                    <div id="imagePreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

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
                            <span class="text-sm text-gray-500">Klik untuk pilih gambar (bisa lebih dari 1)</span>
                            <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="imageInput"
                                onchange="previewImages(this)">
                        </label>
                    </div>

                    <div id="image-url-section" class="hidden">
                        <div id="image-url-list" class="space-y-2 mb-3">
                            <div class="image-url-entry flex gap-2 items-start" data-index="0">
                                <a href="#" target="_blank" class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0 opacity-30 cursor-not-allowed" title="Buka link" onclick="return false;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                <input type="text" name="image_urls[]" class="form-input flex-1"
                                    placeholder="https://example.com/image.jpg atau link Google Drive" data-index="0" oninput="updateUrlLink(this)">
                                <button type="button" onclick="removeImageUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addImageUrlEntry()"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah URL Gambar
                        </button>
                    </div>

                    <div id="urlImagePreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

                    <div id="infoPopupImageArea">
                        <label class="form-label mt-2">Keterangan Info Popup per Gambar <span
                                class="text-gray-400 text-xs">(klik tombol ? akan menampilkan teks ini)</span></label>
                        <div id="infoPopupRows" class="space-y-2">
                            <p class="text-xs text-gray-400 italic" id="noImagesHint">Upload atau masukkan URL gambar dulu untuk mengisi keterangan popup.</p>
                        </div>
                    </div>
                </div>

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
                            <div class="carousel-video-url-entry flex gap-2 items-start" data-index="0">
                                <input type="text" name="carousel_video_urls[]" class="form-input flex-1"
                                    placeholder="https://youtube.com/watch?v=... atau link Google Drive" data-index="0" data-caption="" oninput="updateCarouselUrlCaption(this)">
                                <button type="button" onclick="removeCarouselVideoUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
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
                        <input type="hidden" name="unified_video_order" id="unifiedVideoOrderInput" value="">
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

            {{-- Step 4: Video --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4 section-panel"
                id="panel-video">
                <h2 class="text-base font-semibold text-gray-800">4. Video</h2>

                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="url" checked onchange="toggleVideoMethod('url')">
                        <span class="text-sm text-gray-700">URL</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="upload" onchange="toggleVideoMethod('upload')">
                        <span class="text-sm text-gray-700">Upload File</span>
                    </label>
                </div>

                <div id="video-url-section">
                    <div class="flex gap-2 items-start">
                        <input type="text" name="video_url" class="form-input flex-1"
                            placeholder="https://youtube.com/watch?v=..., Google Drive, atau URL video lainnya" oninput="previewVideoUrl(this)">
                        <div class="url-preview-placeholder w-24 h-16 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <span class="text-xs text-gray-400">Preview</span>
                        </div>
                    </div>
                </div>

                <div id="video-upload-section" class="hidden">
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

                <div>
                    <label class="form-label">Keterangan Info Popup Video</label>
                    <input type="text" name="info_popup_video" class="form-input"
                        placeholder="Keterangan yang muncul saat tombol ? diklik..."
                        value="{{ old('info_popup_video') }}">
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
                    Simpan Slide
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://richtexteditor.com/richtexteditor/rte.js"></script>
    <script type="text/javascript" src="https://richtexteditor.com/richtexteditor/plugins/all_plugins.js"></script>
    <script>
        (function() {
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
                    // Show toggle and default to images
                    if (carouselToggle) carouselToggle.classList.remove('hidden');
                    if (imageSections) imageSections.classList.remove('hidden');
                    if (videoSections) videoSections.classList.add('hidden');
                    // Reset to images
                    var imgRadio = carouselToggle ? carouselToggle.querySelector('input[value="images"]') : null;
                    if (imgRadio) imgRadio.checked = true;
                    toggleCarouselMediaType('images');
                } else {
                    // Hide toggle, show images by default
                    if (carouselToggle) carouselToggle.classList.add('hidden');
                    if (imageSections) imageSections.classList.remove('hidden');
                    if (videoSections) videoSections.classList.add('hidden');
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
                if (method === 'url') {
                    uploadSection.classList.add('hidden');
                    urlSection.classList.remove('hidden');
                } else {
                    uploadSection.classList.remove('hidden');
                    urlSection.classList.add('hidden');
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
                    '<input type="text" name="image_urls[]" class="form-input flex-1" ' +
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

                updateUrlImagePreviews();
            };

            window.removeImageUrlEntry = function(btn) {
                var entries = document.querySelectorAll('.image-url-entry');
                if (entries.length > 1) {
                    btn.closest('.image-url-entry').remove();
                    // Also remove the corresponding caption field
                    updateUrlImagePreviews();
                }
            };

            // Array to track URL images
            var urlImageEntries = [];

            window.previewImageUrl = function(input) {
                var url = input.value.trim();
                updateUrlImagePreviews();
            };

            function updateUrlImagePreviews() {
                var previewArea = document.getElementById('urlImagePreviewArea');
                var popupRows = document.getElementById('infoPopupRows');
                var popupArea = document.getElementById('infoPopupImageArea');
                var hint = document.getElementById('noImagesHint');
                var uploadPreviewArea = document.getElementById('imagePreviewArea');

                // Get all URL inputs
                var urlInputs = document.querySelectorAll('#image-url-list input[name="image_urls[]"]');
                var urlImages = [];

                // Helper function to convert Google Drive URL to direct image URL
                function convertGoogleDriveUrl(url) {
                    // Format: https://drive.google.com/file/d/FILE_ID/view
                    var match = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
                    if (match) {
                        return 'https://lh3.googleusercontent.com/d/' + match[1];
                    }
                    // Format: https://drive.google.com/open?id=FILE_ID
                    match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                    if (match) {
                        return 'https://lh3.googleusercontent.com/d/' + match[1];
                    }
                    // Format: https://drive.google.com/uc?export=view&id=FILE_ID (convert to new format)
                    match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                    if (url.includes('drive.google.com/uc?')) {
                        match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                        if (match) {
                            return 'https://lh3.googleusercontent.com/d/' + match[1];
                        }
                    }
                    return url;
                }

                urlInputs.forEach(function(input, idx) {
                    var url = input.value.trim();
                    var entry = input.closest('.image-url-entry');
                    var linkBtn = entry ? entry.querySelector('.url-link-btn') : null;

                    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                        // Update link button
                        if (linkBtn) {
                            linkBtn.href = url;
                            linkBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                            linkBtn.onclick = null;
                        }
                        // Convert Google Drive URL if needed
                        var displayUrl = convertGoogleDriveUrl(url);
                        urlImages.push({ url: displayUrl, originalUrl: url });
                    } else {
                        // Reset link button
                        if (linkBtn) {
                            linkBtn.href = '#';
                            linkBtn.classList.add('opacity-30', 'cursor-not-allowed');
                            linkBtn.onclick = function() { return false; };
                        }
                    }
                });

                // Get uploaded images count
                var uploadedCount = selectedImageFiles.length;
                var totalImages = urlImages.length + uploadedCount;

                // Clear preview areas
                previewArea.innerHTML = '';

                if (totalImages === 0) {
                    if (hint) hint.style.display = '';
                    if (popupArea) popupArea.style.display = 'none';
                    popupRows.innerHTML = '<p class="text-xs text-gray-400 italic" id="noImagesHint">Upload atau masukkan URL gambar dulu untuk mengisi keterangan popup.</p>';
                    return;
                }

                if (hint) hint.style.display = 'none';
                if (popupArea) popupArea.style.display = 'block';
                popupRows.innerHTML = '';

                // Render URL image previews
                urlImages.forEach(function(img, idx) {
                    var wrap = document.createElement('div');
                    wrap.className = 'img-preview-wrap';

                    var imgIndex = idx; // Index for info_popup
                    var isGoogleDrive = img.originalUrl.includes('drive.google.com');

                    if (isGoogleDrive) {
                        // For Google Drive, show a placeholder with link
                        wrap.innerHTML = '<div class="flex flex-col items-center justify-center" style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;">' +
                            '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' +
                            '<a href="' + img.originalUrl + '" target="_blank" class="text-xs text-blue-500 hover:text-blue-700 mt-1">Lihat</a></div>' +
                            '<button type="button" class="remove-img" onclick="removeUrlImage(' + idx + ')">✕</button>';
                    } else {
                        // For regular URLs, try to load the image
                        wrap.innerHTML = '<img src="' + img.url + '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">' +
                            '<div class="flex items-center justify-center" style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:none;"><span class="text-xs text-gray-400">Error</span></div>' +
                            '<button type="button" class="remove-img" onclick="removeUrlImage(' + idx + ')">✕</button>';
                    }
                    previewArea.appendChild(wrap);

                    // Add caption input - index matches the URL position in the list
                    var row = document.createElement('div');
                    row.className = 'info-popup-row';
                    row.innerHTML = '<label class="form-label" style="margin-bottom:4px;">Gambar URL ' + (idx + 1) + '</label>' +
                        '<input type="text" name="info_popup_images[' + imgIndex + ']" class="form-input" placeholder="Keterangan gambar ' + (idx + 1) + ' (opsional)...">';
                    popupRows.appendChild(row);
                });

                // Update uploaded image caption indices to come after URL images
                selectedImageFiles.forEach(function(file, idx) {
                    var reader = new FileReader();
                    var imgIndex = urlImages.length + idx;
                    (function(index, fileReader) {
                        fileReader.onload = function(e) {
                            var wrap = document.createElement('div');
                            wrap.className = 'img-preview-wrap';
                            wrap.innerHTML = '<img src="' + e.target.result + '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">' +
                                '<button type="button" class="remove-img" onclick="removePreviewImage(' + index + ')">✕</button>';
                            previewArea.appendChild(wrap);
                        };
                    })(idx, reader);
                    reader.readAsDataURL(file);

                    // Add caption input with correct index
                    var row = document.createElement('div');
                    row.className = 'info-popup-row';
                    row.innerHTML = '<label class="form-label" style="margin-bottom:4px;">Gambar Upload ' + (idx + 1) + '</label>' +
                        '<input type="text" name="info_popup_images[' + imgIndex + ']" class="form-input" placeholder="Keterangan gambar ' + (idx + 1) + ' (opsional)...">';
                    popupRows.appendChild(row);
                });
            }

            window.removeUrlImage = function(idx) {
                var inputs = document.querySelectorAll('#image-url-list input[name="image_urls[]"]');
                if (inputs[idx]) {
                    inputs[idx].value = '';
                }
                updateUrlImagePreviews();
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

            var selectedImageFiles = [];

            window.previewImages = function(input) {
                var files = Array.from(input.files);

                if (files.length === 0) return;

                files.forEach(function(file) {
                    selectedImageFiles.push(file);
                });

                renderImagePreviews();
            };

            function renderImagePreviews() {
                updateUrlImagePreviews();
            }

            window.removePreviewImage = function(idx) {
                selectedImageFiles.splice(idx, 1);
                renderImagePreviews();
            };

            // Carousel Video Variables and Functions
            var keptCarouselCaptions = {};
            var selectedCarouselVideoFiles = [];

            window.toggleCarouselVideoMethod = function(method) {
                document.getElementById('carousel-video-url-section').classList.toggle('hidden', method !== 'url');
                document.getElementById('carousel-video-upload-section').classList.toggle('hidden', method !== 'upload');
            };

            window.addCarouselVideoUrlEntry = function() {
                var list = document.getElementById('carousel-video-url-list');
                var entries = list.querySelectorAll('.carousel-video-url-entry');
                var newIndex = entries.length;
                var currentTime = Date.now();

                var entry = document.createElement('div');
                entry.className = 'carousel-video-url-entry flex gap-2 items-start';
                entry.setAttribute('data-index', newIndex);
                entry.innerHTML =
                    '<input type="text" name="carousel_video_urls[' + newIndex + ']" class="form-input flex-1" ' +
                    'placeholder="https://youtube.com/watch?v=... atau link Google Drive" data-index="' + newIndex + '" data-caption="" oninput="updateCarouselUrlCaption(this)">' +
                    '<button type="button" onclick="removeCarouselVideoUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
                list.appendChild(entry);

                // Add to unifiedVideoOrder with timestamp
                unifiedVideoOrder.push({ type: 'url', urlIndex: newIndex, urlValue: '', order: currentTime });
                updateUnifiedVideoOrderInput();
            };

            window.updateCarouselUrlCaption = function(input) {
                updateCarouselVideoPreviews();
            };

            window.removeCarouselVideoUrlEntry = function(btn) {
                var entry = btn.closest('.carousel-video-url-entry');
                var entries = document.querySelectorAll('.carousel-video-url-entry');
                var urlIndex = entry ? parseInt(entry.getAttribute('data-index')) : -1;

                if (entries.length > 1) {
                    // Remove caption from keptCarouselCaptions
                    if (urlIndex >= 0) {
                        delete keptCarouselCaptions['url_' + urlIndex];
                        // Remove from unifiedVideoOrder
                        unifiedVideoOrder = unifiedVideoOrder.filter(function(v) {
                            return !(v.type === 'url' && v.urlIndex === urlIndex);
                        });
                    }
                    entry.remove();
                    // Reindex remaining entries to maintain proper order
                    var remainingEntries = document.querySelectorAll('.carousel-video-url-entry');
                    remainingEntries.forEach(function(ent, newIdx) {
                        var input = ent.querySelector('input[name^="carousel_video_urls"]');
                        if (input) {
                            var oldIndex = parseInt(input.getAttribute('data-index'));
                            // Migrate caption to new key
                            var oldCaptionKey = 'url_' + oldIndex;
                            var newCaptionKey = 'url_' + newIdx;
                            if (keptCarouselCaptions[oldCaptionKey] && oldIndex !== newIdx) {
                                keptCarouselCaptions[newCaptionKey] = keptCarouselCaptions[oldCaptionKey];
                                delete keptCarouselCaptions[oldCaptionKey];
                            }
                            input.setAttribute('data-index', newIdx);
                            input.name = 'carousel_video_urls[' + newIdx + ']';
                            // Update urlIndex in unifiedVideoOrder
                            unifiedVideoOrder.forEach(function(v) {
                                if (v.type === 'url' && v.urlIndex === oldIndex) {
                                    v.urlIndex = newIdx;
                                }
                            });
                        }
                    });
                } else {
                    // Just clear the value if it's the last entry
                    var inputs = document.querySelectorAll('#carousel-video-url-list input[name^="carousel_video_urls"]');
                    if (inputs[0]) {
                        inputs[0].value = '';
                        inputs[0].setAttribute('data-caption', '');
                    }
                    // Clear caption from keptCarouselCaptions and update unifiedVideoOrder
                    if (urlIndex >= 0) {
                        delete keptCarouselCaptions['url_' + urlIndex];
                        unifiedVideoOrder.forEach(function(v) {
                            if (v.type === 'url' && v.urlIndex === urlIndex) {
                                v.urlValue = '';
                            }
                        });
                    }
                }
                updateUnifiedVideoOrderInput();
                updateCarouselVideoPreviews();
            };

            window.previewCarouselVideoUrl = function(input) {
                updateCarouselVideoPreviews();
            };

            window.previewCarouselVideos = function(input) {
                var files = Array.from(input.files);

                if (files.length === 0) return;

                files.forEach(function(file) {
                    var currentTime = Date.now();
                    var fileIndex = selectedCarouselVideoFiles.length;
                    selectedCarouselVideoFiles.push(file);
                    // Add to unifiedVideoOrder with timestamp
                    unifiedVideoOrder.push({ type: 'newUpload', newUploadIndex: fileIndex, file: file, order: currentTime });
                });

                updateUnifiedVideoOrderInput();
                renderCarouselVideoPreviews();
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

                // Sync URL entries from DOM to unifiedVideoOrder
                var urlEntries = document.querySelectorAll('.carousel-video-url-entry');

                // Update existing URL entries in unifiedVideoOrder
                urlEntries.forEach(function(entry) {
                    var input = entry.querySelector('input[name^="carousel_video_urls"]');
                    if (input) {
                        var urlIndex = parseInt(input.getAttribute('data-index'));
                        var urlValue = input.value.trim();

                        // Find and update existing entry
                        var existingEntry = unifiedVideoOrder.find(function(v) {
                            return v.type === 'url' && v.urlIndex === urlIndex;
                        });

                        if (existingEntry) {
                            existingEntry.urlValue = urlValue;
                        }
                    }
                });

                // Sort unifiedVideoOrder by order timestamp
                unifiedVideoOrder.sort(function(a, b) {
                    return a.order - b.order;
                });

                // Filter videos that have content for display
                var displayableVideos = unifiedVideoOrder.filter(function(video) {
                    if (video.type === 'url') {
                        return video.urlValue && (video.urlValue.startsWith('http://') || video.urlValue.startsWith('https://'));
                    }
                    return true;
                });

                var totalVideos = displayableVideos.length;

                // Clear preview areas
                previewArea.innerHTML = '';

                if (totalVideos === 0) {
                    if (hint) hint.style.display = '';
                    popupRows.innerHTML = '<p class="text-xs text-gray-400 italic" id="noCarouselVideosHint">Tambah video dulu untuk mengisi keterangan popup.</p>';
                    return;
                }

                if (hint) hint.style.display = 'none';
                popupRows.innerHTML = '';

                // Render videos based on sorted unifiedVideoOrder
                var displayIndex = 0;
                var processedNewUploads = {};

                unifiedVideoOrder.forEach(function(video) {
                    // Only render previews for videos that have actual content
                    var hasContent = (video.type === 'url' && video.urlValue && (video.urlValue.startsWith('http://') || video.urlValue.startsWith('https://'))) ||
                                     video.type === 'newUpload';

                    if (!hasContent) {
                        return; // Skip entries without content
                    }

                    var wrap = document.createElement('div');
                    wrap.className = 'img-preview-wrap';

                    if (video.type === 'url') {
                        var youtubeId = getYouTubeId(video.urlValue);
                        if (youtubeId) {
                            wrap.innerHTML = '<img src="https://img.youtube.com/vi/' + youtubeId + '/1.jpg" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" class="rounded-lg">' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.urlIndex + ')">✕</button>';
                        } else if (video.urlValue.endsWith('.mp4') || video.urlValue.endsWith('.webm') || video.urlValue.endsWith('.ogg')) {
                            wrap.innerHTML = '<video src="' + video.urlValue + '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.urlIndex + ')">✕</button>';
                        } else if (video.urlValue.includes('drive.google.com')) {
                            var gdThumb = convertGoogleDriveUrl(video.urlValue);
                            if (gdThumb) {
                                wrap.innerHTML = '<img src="' + gdThumb + '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">' +
                                    '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center" style="display:none;"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.urlIndex + ')">✕</button>';
                            } else {
                                wrap.innerHTML = '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.urlIndex + ')">✕</button>';
                            }
                        } else {
                            wrap.innerHTML = '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video.urlIndex + ')">✕</button>';
                        }

                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var captionKey = 'url_' + video.urlIndex;
                        row.innerHTML = '<label class="form-label" style="margin-bottom:4px;">Video ' + (displayIndex + 1) + '</label>' +
                            '<input type="text" name="info_popup_carousel_videos[' + captionKey + ']" class="form-input" placeholder="Keterangan video ' + (displayIndex + 1) + ' (opsional)...">';
                        popupRows.appendChild(row);
                        previewArea.appendChild(wrap);
                        displayIndex++;

                    } else if (video.type === 'newUpload') {
                        // Track processed new uploads to avoid duplicates
                        if (processedNewUploads[video.newUploadIndex]) {
                            return;
                        }
                        processedNewUploads[video.newUploadIndex] = true;

                        var videoFile = video.file || selectedCarouselVideoFiles[video.newUploadIndex];
                        if (videoFile) {
                            var reader = new FileReader();
                            (function(displayIdx, file, videoIdx) {
                                reader.onload = function(e) {
                                    var newWrap = document.createElement('div');
                                    newWrap.className = 'img-preview-wrap';
                                    newWrap.innerHTML = '<video src="' + e.target.result + '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                                        '<button type="button" class="remove-img" onclick="removePreviewVideo(' + videoIdx + ')">✕</button>';
                                    previewArea.appendChild(newWrap);
                                };
                            })(displayIndex, videoFile, video.newUploadIndex);
                            reader.readAsDataURL(videoFile);

                            var row = document.createElement('div');
                            row.className = 'info-popup-row';
                            var captionKey = 'newUpload_' + video.newUploadIndex;
                            row.innerHTML = '<label class="form-label" style="margin-bottom:4px;">Video ' + (displayIndex + 1) + '</label>' +
                                '<input type="text" name="info_popup_carousel_videos[' + captionKey + ']" class="form-input" placeholder="Keterangan video ' + (displayIndex + 1) + ' (opsional)...">';
                            popupRows.appendChild(row);
                            displayIndex++;
                        }
                    }
                });
            }

            window.removeUrlVideo = function(idx) {
                var entries = document.querySelectorAll('.carousel-video-url-entry');
                var targetEntry = null;
                entries.forEach(function(entry) {
                    if (parseInt(entry.getAttribute('data-index')) === idx) {
                        targetEntry = entry;
                    }
                });
                if (targetEntry) {
                    var inputs = targetEntry.querySelectorAll('input[name^="carousel_video_urls"]');
                    if (inputs.length > 0) {
                        inputs[0].value = '';
                        inputs[0].setAttribute('data-caption', '');
                    }
                    // Delete caption from keptCarouselCaptions
                    delete keptCarouselCaptions['url_' + idx];
                    // Update unifiedVideoOrder
                    unifiedVideoOrder.forEach(function(v) {
                        if (v.type === 'url' && v.urlIndex === idx) {
                            v.urlValue = '';
                        }
                    });
                }
                updateUnifiedVideoOrderInput();
                updateCarouselVideoPreviews();
            }

            function renderCarouselVideoPreviews() {
                updateCarouselVideoPreviews();
            }

            window.removePreviewVideo = function(idx) {
                // Remove caption for this new upload
                delete keptCarouselCaptions['newUpload_' + idx];

                // Remove from unifiedVideoOrder first
                unifiedVideoOrder = unifiedVideoOrder.filter(function(v) {
                    return !(v.type === 'newUpload' && v.newUploadIndex === idx);
                });
                // Reindex remaining newUpload entries and migrate captions
                unifiedVideoOrder.forEach(function(v) {
                    if (v.type === 'newUpload' && v.newUploadIndex > idx) {
                        // Migrate caption to new index
                        var oldCaptionKey = 'newUpload_' + v.newUploadIndex;
                        var newCaptionKey = 'newUpload_' + (v.newUploadIndex - 1);
                        if (keptCarouselCaptions[oldCaptionKey]) {
                            keptCarouselCaptions[newCaptionKey] = keptCarouselCaptions[oldCaptionKey];
                            delete keptCarouselCaptions[oldCaptionKey];
                        }
                        v.newUploadIndex--;
                    }
                });
                // Remove from selectedCarouselVideoFiles
                selectedCarouselVideoFiles.splice(idx, 1);
                updateUnifiedVideoOrderInput();
                renderCarouselVideoPreviews();
            };

            selectType('text');

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
                if (imageInput && selectedImageFiles.length > 0) {
                    var imageDataTransfer = new DataTransfer();
                    selectedImageFiles.forEach(function(file) {
                        imageDataTransfer.items.add(file);
                    });
                    imageInput.files = imageDataTransfer.files;
                    console.log('Set imageInput files:', imageInput.files.length);
                }

                // Set files on the existing carousel video input
                var carouselInput = document.getElementById('carouselVideoInput');
                if (carouselInput && selectedCarouselVideoFiles.length > 0) {
                    var videoDataTransfer = new DataTransfer();
                    selectedCarouselVideoFiles.forEach(function(file) {
                        videoDataTransfer.items.add(file);
                    });
                    carouselInput.files = videoDataTransfer.files;
                    console.log('Set carouselInput files:', carouselInput.files.length);
                }
            });
        })();
    </script>
@endpush
