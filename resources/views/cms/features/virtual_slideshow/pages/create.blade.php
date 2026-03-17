@extends('layouts.app')

@section('breadcrumb_parent', 'CMS / ' . __('cms.features.title') . ' / <a href="' . route('cms.features.slideshow.index', $feature) . '">' . ($page ?? null ? ($page->title . ' - Slides') : __('cms.virtual_slideshow.title')) . '</a>')
@section('breadcrumb_active', 'Tambah Slide')

@push('styles')
<link rel="stylesheet" href="https://richtexteditor.com/richtexteditor/rte_theme_default.css" />
<style>
.slide-type-card { cursor:pointer; border:2px solid #e5e7eb; border-radius:12px; padding:1rem; text-align:center; transition:all 0.2s; }
.slide-type-card:hover { border-color:#174E93; background:#f0f5ff; }
.slide-type-card.active { border-color:#174E93; background:#eef3ff; }
.slide-type-card .icon { font-size:2rem; margin-bottom:0.5rem; }
.slide-type-card .label { font-size:0.8rem; font-weight:600; color:#374151; }
.slide-type-card .desc { font-size:0.7rem; color:#9ca3af; margin-top:2px; }
.section-panel { display:none; }
.section-panel.active { display:block; }
.form-label { display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1px solid #e5e7eb; border-radius:8px; font-size:0.875rem; outline:none; transition:border-color 0.15s; }
.form-input:focus { border-color:#174E93; box-shadow:0 0 0 3px rgba(23,78,147,0.1); }
.info-popup-row { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px; margin-bottom:8px; }
.img-preview-wrap { position:relative; display:inline-block; }
.img-preview-wrap img { height:80px; width:80px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; }
.img-preview-wrap .remove-img { position:absolute; top:-6px; right:-6px; background:#ef4444; color:white; border:none; border-radius:50%; width:18px; height:18px; font-size:10px; display:flex;align-items:center;justify-content:center; cursor:pointer; }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm" style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Slide Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
            @if(isset($page))
            <p class="text-sm text-blue-600 mt-0.5">Halaman: {{ $page->title }}</p>
            @endif
        </div>
    </div>

    <form action="{{ isset($page) ? route('cms.features.slideshow.pages.slides.store', [$feature, $page]) : route('cms.features.slideshow.store', $feature) }}" method="POST" enctype="multipart/form-data" id="slideForm">
        @csrf

        @if(isset($page))
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
                    <input type="text" name="title" class="form-input" placeholder="Judul slide..." value="{{ old('title') }}">
                </div>
                <div>
                    <label class="form-label">Sub-judul <span class="text-gray-400 text-xs">(opsional)</span></label>
                    <input type="text" name="subtitle" class="form-input" placeholder="Sub-judul..." value="{{ old('subtitle') }}">
                </div>
            </div>

            <div>
                <label class="form-label">Deskripsi / Teks Konten <span class="text-gray-400 text-xs">(opsional - gunakan toolbar untuk format)</span></label>
                <div id="div_editor1" style="min-width:100%;">{!! old('description') !!}</div>
                <input type="hidden" name="description" id="hiddenDescription">
            </div>

            {{-- Layout --}}
            <div class="panel-layout" style="display:none;">
                <label class="form-label">Layout</label>
                <div class="flex gap-3">
                    @foreach(['left'=>'Gambar Kiri, Teks Kanan','center'=>'Tengah','right'=>'Teks Kiri, Gambar Kanan'] as $val=>$lbl)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="layout" value="{{ $val }}" {{ old('layout','center') === $val ? 'checked' : '' }}>
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
                        <input type="color" name="bg_color" value="{{ old('bg_color','#ffffff') }}" class="w-10 h-10 rounded border border-gray-200 cursor-pointer">
                        <input type="text" id="bg_color_text" value="{{ old('bg_color','#ffffff') }}" class="form-input" style="width:140px;"
                            onchange="document.querySelector('[name=bg_color]').value=this.value">
                    </div>
                </div>
                <div>
                    <label class="form-label">Urutan</label>
                    <input type="number" name="order" min="0" value="{{ old('order', 1) }}" class="form-input" required>
                </div>
            </div>
        </div>

        {{-- Step 3: Media --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4 section-panel active" id="panel-images">
            <h2 class="text-base font-semibold text-gray-800">3. Gambar</h2>

            <div id="imagePreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

            <label class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-500">Klik untuk pilih gambar (bisa lebih dari 1)</span>
                <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="imageInput" onchange="previewImages(this)">
            </label>

            <div id="infoPopupImageArea">
                <label class="form-label mt-2">Keterangan Info Popup per Gambar <span class="text-gray-400 text-xs">(klik tombol ? akan menampilkan teks ini)</span></label>
                <div id="infoPopupRows" class="space-y-2">
                    <p class="text-xs text-gray-400 italic" id="noImagesHint">Upload gambar dulu untuk mengisi keterangan popup.</p>
                </div>
            </div>
        </div>

        {{-- Step 4: Video --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4 section-panel" id="panel-video">
            <h2 class="text-base font-semibold text-gray-800">4. Video</h2>
            <div>
                <label class="form-label">URL Video <span class="text-gray-400 text-xs">(YouTube, atau URL .mp4)</span></label>
                <input type="url" name="video_url" class="form-input" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_url') }}">
            </div>
            <div>
                <label class="form-label">Keterangan Info Popup Video</label>
                <input type="text" name="info_popup_video" class="form-input" placeholder="Keterangan yang muncul saat tombol ? diklik..." value="{{ old('info_popup_video') }}">
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
    // ---- Type config ----
    var typeConfig = {
        hero:          { showImages: true,  showVideo: false, showLayout: false },
        text:          { showImages: false, showVideo: false, showLayout: false },
        carousel:      { showImages: true,  showVideo: false, showLayout: false },
        video:         { showImages: false, showVideo: true,  showLayout: false },
        text_carousel: { showImages: true,  showVideo: false, showLayout: true  },
    };

    window.selectType = function(type) {
        document.getElementById('slide_type_input').value = type;
        document.querySelectorAll('.slide-type-card').forEach(function(c) { c.classList.remove('active'); });
        var card = document.querySelector('.slide-type-card[data-type="' + type + '"]');
        if (card) card.classList.add('active');

        var cfg = typeConfig[type];
        document.getElementById('panel-images').style.display = cfg.showImages ? 'block' : 'none';
        document.getElementById('panel-video').style.display  = cfg.showVideo  ? 'block' : 'none';
        document.querySelectorAll('.panel-layout').forEach(function(el) { el.style.display = cfg.showLayout ? 'block' : 'none'; });
        var hiddenLayout = document.getElementById('layout_center_hidden');
        if (hiddenLayout) hiddenLayout.disabled = cfg.showLayout;
    };

    window.previewImages = function(input) {
        var previewArea = document.getElementById('imagePreviewArea');
        var popupRows   = document.getElementById('infoPopupRows');
        var hint        = document.getElementById('noImagesHint');
        var files       = Array.from(input.files);

        previewArea.innerHTML = '';
        popupRows.innerHTML   = '';

        if (files.length === 0) {
            if (hint) hint.style.display = '';
            return;
        }
        if (hint) hint.style.display = 'none';

        files.forEach(function(file, idx) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var wrap = document.createElement('div');
                wrap.className = 'img-preview-wrap';
                wrap.innerHTML = '<img src="' + e.target.result + '" alt="">';
                previewArea.appendChild(wrap);
            };
            reader.readAsDataURL(file);

            var row = document.createElement('div');
            row.className = 'info-popup-row';
            row.innerHTML = '<label class="form-label" style="margin-bottom:4px;">Gambar ' + (idx+1) + '</label>' +
                '<input type="text" name="info_popup_images[' + idx + ']" class="form-input" placeholder="Keterangan gambar ' + (idx+1) + ' (opsional)...">';
            popupRows.appendChild(row);
        });
    };

    // ---- Init: hide panels ----
    selectType('text');

    // ---- Sync color picker ----
    var bgColorInput = document.querySelector('[name=bg_color]');
    if (bgColorInput) {
        bgColorInput.addEventListener('input', function() {
            document.getElementById('bg_color_text').value = this.value;
        });
    }

    // ---- RichTextEditor init with retry ----
    var editor1 = null;

    function initRTE() {
        if (typeof RichTextEditor === 'undefined') {
            setTimeout(initRTE, 200);
            return;
        }
        try {
            editor1 = new RichTextEditor("#div_editor1");
        } catch(e) {
            console.error('RTE init error:', e);
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initRTE();
    } else {
        window.addEventListener('load', initRTE);
    }

    // ---- Form submit: sync editor content ----
    document.getElementById('slideForm').addEventListener('submit', function() {
        if (editor1) {
            try {
                document.getElementById('hiddenDescription').value = editor1.getHTMLCode();
            } catch(e) {
                try {
                    document.getElementById('hiddenDescription').value = editor1.getHTML();
                } catch(e2) {
                    console.error('RTE getHTML error:', e2);
                }
            }
        }
    });
})();
</script>
@endpush