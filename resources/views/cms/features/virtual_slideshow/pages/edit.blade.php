@extends('layouts.app')

@section('breadcrumb_parent', 'CMS / ' . __('cms.features.title') . ' / ' . $feature->name . ' / ' . $page->title)
@section('breadcrumb_parent_url', route('cms.features.slideshow.index', $feature))
@section('breadcrumb_active', 'Edit Slide')

@push('styles')
<link rel="stylesheet" href="https://richtexteditor.com/richtexteditor/rte_theme_default.css" />
<style>
.slide-type-card { cursor:pointer; border:2px solid #e5e7eb; border-radius:12px; padding:1rem; text-align:center; transition:all 0.2s; }
.slide-type-card:hover { border-color:#174E93; background:#f0f5ff; }
.slide-type-card.active { border-color:#174E93; background:#eef3ff; }
.slide-type-card .icon { font-size:2rem; margin-bottom:0.5rem; }
.slide-type-card .label { font-size:0.8rem; font-weight:600; color:#374151; }
.slide-type-card .desc { font-size:0.7rem; color:#9ca3af; margin-top:2px; }
.form-label { display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1px solid #e5e7eb; border-radius:8px; font-size:0.875rem; outline:none; transition:border-color 0.15s; }
.form-input:focus { border-color:#174E93; box-shadow:0 0 0 3px rgba(23,78,147,0.1); }
.info-popup-row { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px; margin-bottom:8px; }
.existing-img-wrap { position:relative; display:inline-block; margin:4px; }
.existing-img-wrap img { height:80px; width:80px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; }
.existing-img-wrap .remove-existing { position:absolute; top:-6px; right:-6px; background:#ef4444; color:white; border:none; border-radius:50%; width:18px; height:18px; font-size:10px; display:flex;align-items:center;justify-content:center; cursor:pointer; }
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
            <h1 class="text-2xl font-bold text-gray-800">Edit Slide</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
            @if(isset($page))
            <p class="text-sm text-blue-600 mt-0.5">Halaman: {{ $page->title }}</p>
            @endif
        </div>
    </div>

    <form action="{{ isset($page) ? route('cms.features.slideshow.pages.slides.update', [$feature, $page, $slide]) : route('cms.features.slideshow.update', [$feature, $slide]) }}" method="POST" enctype="multipart/form-data" id="slideForm">
        @csrf @method('PUT')

        @if(isset($page))
        <input type="hidden" name="feature_page_id" value="{{ $page->id }}">
        @endif

        {{-- Tipe Slide --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-800">1. Tipe Slide</h2>
            <input type="hidden" name="slide_type" id="slide_type_input" value="{{ $slide->slide_type }}">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                @foreach(['text'=>['📝','Teks','Konten teks saja'],'hero'=>['🌟','Hero','Banner pembuka'],'carousel'=>['🖼️','Carousel','Slideshow gambar'],'video'=>['🎬','Video','Embed video'],'text_carousel'=>['📋','Teks + Carousel','Split layout']] as $type=>$info)
                <div class="slide-type-card {{ $slide->slide_type === $type ? 'active' : '' }}" data-type="{{ $type }}" onclick="selectType('{{ $type }}')">
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
                    <input type="text" name="subtitle" class="form-input" value="{{ old('subtitle', $slide->subtitle) }}">
                </div>
            </div>
            <div>
                <label class="form-label">Deskripsi / Teks Konten <span class="text-gray-400 text-xs">(gunakan toolbar untuk format)</span></label>
                <div id="div_editor1" style="min-width:100%;">{!! old('description', $slide->description) !!}</div>
                <input type="hidden" name="description" id="hiddenDescription">
            </div>

            <div class="panel-layout" style="display:none;">
                <label class="form-label">Layout</label>
                <div class="flex gap-3">
                    @foreach(['left'=>'Gambar Kiri, Teks Kanan','center'=>'Tengah','right'=>'Teks Kiri, Gambar Kanan'] as $val=>$lbl)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="layout" value="{{ $val }}" {{ old('layout', $slide->layout) === $val ? 'checked' : '' }}>
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
                        <input type="color" name="bg_color" value="{{ old('bg_color', $slide->bg_color ?? '#ffffff') }}" class="w-10 h-10 rounded border border-gray-200 cursor-pointer">
                        <input type="text" id="bg_color_text" value="{{ old('bg_color', $slide->bg_color ?? '#ffffff') }}" class="form-input" style="width:140px;">
                    </div>
                </div>
                <div>
                    <label class="form-label">Urutan</label>
                    <input type="number" name="order" min="0" value="{{ old('order', $slide->order) }}" class="form-input" required>
                </div>
            </div>
        </div>

        {{-- Gambar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4" id="panel-images">
            <h2 class="text-base font-semibold text-gray-800">3. Gambar</h2>

            @if($slide->images && count($slide->images) > 0)
            <div id="existingImagesArea" class="flex flex-wrap gap-2">
                @foreach($slide->images as $idx => $imgPath)
                <div class="existing-img-wrap" id="existing-wrap-{{ $idx }}">
                    <img src="{{ asset('storage/'.$imgPath) }}" alt="">
                    <input type="hidden" name="existing_images[]" value="{{ $imgPath }}" id="existing-input-{{ $idx }}">
                    <button type="button" class="remove-existing" onclick="removeExisting({ $idx })">✕</button>
                </div>
                @endforeach
            </div>

            <div>
                <label class="form-label">Keterangan Info Popup (gambar yang ada)</label>
                @foreach($slide->images as $idx => $imgPath)
                <div class="info-popup-row">
                    <label class="form-label" style="margin-bottom:4px;">Gambar {{ $idx+1 }}</label>
                    <input type="text" name="info_popup_images[{{ $idx }}]" class="form-input"
                        placeholder="Keterangan gambar {{ $idx+1 }}..."
                        value="{{ old("info_popup_images.$idx", $slide->info_popup[$idx] ?? $slide->info_popup[(string)$idx] ?? '') }}">
                </div>
                @endforeach
            </div>
            @endif

            <label class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm text-gray-500">Tambah gambar baru</span>
                <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="imageInput" onchange="previewNewImages(this)">
            </label>
            <div id="newImagePreviewArea" class="flex flex-wrap gap-3"></div>
            <div id="newInfoPopupRows" class="space-y-2"></div>
        </div>

        {{-- Video --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4" id="panel-video">
            <h2 class="text-base font-semibold text-gray-800">4. Video</h2>
            <div>
                <label class="form-label">URL Video</label>
                <input type="url" name="video_url" class="form-input" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_url', $slide->video_url) }}">
            </div>
            <div>
                <label class="form-label">Keterangan Info Popup Video</label>
                <input type="text" name="info_popup_video" class="form-input"
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

    window.removeExisting = function(idx) {
        var wrap = document.getElementById('existing-wrap-' + idx);
        if (wrap) wrap.remove();
        var inp = document.getElementById('existing-input-' + idx);
        if (inp) inp.remove();
    };

    window.previewNewImages = function(input) {
        var previewArea = document.getElementById('newImagePreviewArea');
        var popupRows   = document.getElementById('newInfoPopupRows');
        var files       = Array.from(input.files);
        previewArea.innerHTML = '';
        popupRows.innerHTML   = '';

        var existingCount = document.querySelectorAll('[name="existing_images[]"]').length;

        files.forEach(function(file, i) {
            var idx = existingCount + i;
            var reader = new FileReader();
            reader.onload = function(e) {
                var wrap = document.createElement('div');
                wrap.style.cssText = 'position:relative;display:inline-block;';
                wrap.innerHTML = '<img src="' + e.target.result + '" style="height:80px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" alt="">';
                previewArea.appendChild(wrap);
            };
            reader.readAsDataURL(file);

            var row = document.createElement('div');
            row.className = 'info-popup-row';
            row.innerHTML = '<label class="form-label" style="margin-bottom:4px;">Gambar baru ' + (i+1) + '</label>' +
                '<input type="text" name="info_popup_images[' + idx + ']" class="form-input" placeholder="Keterangan gambar ' + (i+1) + ' baru...">';
            popupRows.appendChild(row);
        });
    };

    // ---- Init type visibility ----
    selectType('{{ $slide->slide_type }}');

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
