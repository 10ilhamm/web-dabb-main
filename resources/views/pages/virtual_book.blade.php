@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<style>
    .vb-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .vb-book-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 2rem;
        margin-top: 2rem;
    }

    .vb-book-controls {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-width: 200px;
    }

    .vb-book {
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.5);
        display: none;
        background-size: cover;
    }

    .vb-page {
        padding: 20px;
        background-color: hsl(35, 55, 98);
        color: hsl(35, 35, 35);
        border: solid 1px hsl(35, 20, 70);
        overflow: hidden;
    }

    .vb-page-content {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: stretch;
    }

    .vb-page-header {
        height: 30px;
        font-size: 100%;
        text-transform: uppercase;
        text-align: center;
    }

    .vb-page-image {
        height: 100%;
        background-size: contain;
        background-position: center center;
        background-repeat: no-repeat;
        flex-grow: 1;
        min-height: 200px;
    }

    .vb-page-text {
        height: 100%;
        flex-grow: 1;
        font-size: 80%;
        text-align: justify;
        margin-top: 10px;
        padding-top: 10px;
        box-sizing: border-box;
        border-top: solid 1px hsl(35, 55, 90);
        overflow-y: auto;
    }

    .vb-page-footer {
        height: 30px;
        border-top: solid 1px hsl(35, 55, 90);
        font-size: 80%;
        color: hsl(35, 20, 50);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .vb-page.--left {
        border-right: 0;
        box-shadow: inset -7px 0 30px -7px rgba(0, 0, 0, 0.4);
    }

    .vb-page.--right {
        border-left: 0;
        box-shadow: inset 7px 0 30px -7px rgba(0, 0, 0, 0.4);
    }

    .vb-page.--right .vb-page-footer {
        justify-content: flex-end;
    }

    .vb-page.hard {
        background-color: hsl(35, 50, 90);
        border: solid 1px hsl(35, 20, 50);
    }

    .vb-page.page-cover {
        background-color: hsl(35, 45, 80);
        color: hsl(35, 35, 35);
        border: solid 1px hsl(35, 20, 50);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .vb-page.page-cover h2 {
        text-align: center;
        font-size: 210%;
    }

    .vb-page.page-cover-top {
        box-shadow: inset 0px 0 30px 0px rgba(36, 10, 3, 0.5), -2px 0 5px 2px rgba(0, 0, 0, 0.4);
    }

    .vb-page.page-cover-bottom {
        box-shadow: inset 0px 0 30px 0px rgba(36, 10, 3, 0.5), 10px 0 8px 0px rgba(0, 0, 0, 0.4);
    }

    .vb-nav-btn {
        padding: 0.75rem 1.5rem;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
    }

    .vb-nav-btn:hover {
        background: #1d4ed8;
    }

    .vb-nav-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .vb-page-info {
        text-align: center;
        padding: 1rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .vb-page-info span {
        font-weight: 600;
    }

    .vb-state {
        font-style: italic;
    }

    @media (max-width: 768px) {
        .vb-book-wrapper {
            flex-direction: column;
            align-items: center;
        }

        .vb-book-controls {
            flex-direction: row;
            width: 100%;
            justify-content: center;
            min-width: auto;
        }
    }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="feature-breadcrumb">
    <div class="container">
        @if($feature->parent)
            <a href="{{ url($feature->parent->path ?? '#') }}">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </a>
            <span class="sep">/</span>
        @endif
        <span class="current">{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</span>
    </div>
</div>

{{-- Blue gradient hero (Pameran Arsip Virtual) --}}
<div class="vt-hero">
    <div class="container">
        @if($feature->parent)
            <p style="font-size:0.8rem;opacity:0.6;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.08em;">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </p>
        @endif
        <h1>{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</h1>
        <p>Virtual Book Exhibition - Flip through pages like a real book</p>
    </div>
</div>

{{-- Book Viewer --}}
<div class="vb-container">
    <div class="vb-book-wrapper">
        <div class="vb-book-controls">
            <button type="button" class="vb-nav-btn" id="btnPrev">
                ← Previous
            </button>

            <div class="vb-page-info">
                Page: <span id="pageCurrent">1</span> of <span id="pageTotal">-</span><br>
                State: <span class="vb-state" id="pageState">read</span>
            </div>

            <button type="button" class="vb-nav-btn" id="btnNext">
                Next →
            </button>
        </div>

        <div class="flip-book" id="demoBookExample">
            @foreach($bookPages as $index => $page)
                @php
                    $images = $page->page_images ?? [];
                    $firstImage = !empty($images) ? $images[0] : null;
                    $imagePositions = $page->image_positions ?? [];
                    $textPosition = $page->text_position ?? ['x' => 0, 'y' => 0, 'width' => 45, 'height' => 30];
                @endphp

                @if($page->is_cover)
                    <div class="vb-page page-cover page-cover-top" data-density="hard">
                        <div class="vb-page-content">
                            <h2>{{ $page->title ?: (app()->getLocale() === 'en' && $page->title_en ? $page->title_en : $feature->name) }}</h2>
                            @if($firstImage)
                            <div class="vb-page-image" style="background-image: url('{{ asset('storage/' . $firstImage) }}'); height: {{ $page->image_height ?? 50 }}%; margin-top: 20px; @if(isset($imagePositions[0])) background-position: {{ $imagePositions[0]['x'] ?? 50 }}% {{ $imagePositions[0]['y'] ?? 50 }}%; @endif"></div>
                            @endif
                        </div>
                    </div>
                @elseif($page->is_back_cover)
                    <div class="vb-page page-cover page-cover-bottom" data-density="hard">
                        <div class="vb-page-content">
                            <h2>{{ $page->title ?: 'THE END' }}</h2>
                            @if($firstImage)
                            <div class="vb-page-image" style="background-image: url('{{ asset('storage/' . $firstImage) }}'); height: {{ $page->image_height ?? 50 }}%; margin-top: 20px; @if(isset($imagePositions[0])) background-position: {{ $imagePositions[0]['x'] ?? 50 }}% {{ $imagePositions[0]['y'] ?? 50 }}%; @endif"></div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="vb-page">
                        <div class="vb-page-content">
                            @if($page->title)
                            <div class="vb-page-header">{{ app()->getLocale() === 'en' && $page->title_en ? $page->title_en : $page->title }}</div>
                            @endif

                            <!-- Multiple images support -->
                            @if(count($images) > 0)
                                @foreach($images as $imgIndex => $image)
                                @php
                                    $pos = $imagePositions[$imgIndex] ?? ['x' => 50, 'y' => 50];
                                @endphp
                                <div class="vb-page-image" style="background-image: url('{{ asset('storage/' . $image) }}'); height: {{ $page->image_height ?? 50 }}%; background-position: {{ $pos['x'] ?? 50 }}% {{ $pos['y'] ?? 50 }}%; margin-bottom: 5px;"></div>
                                @endforeach
                            @endif

                            @if($page->content)
                            <div class="vb-page-text" style="@if($textPosition) margin-top: {{ $textPosition['y'] ?? 0 }}px; margin-left: {{ $textPosition['x'] ?? 0 }}px; width: {{ $textPosition['width'] ?? 45 }}%; height: {{ $textPosition['height'] ?? 30 }}%; @endif">
                                {!! nl2br(e(app()->getLocale() === 'en' && $page->content_en ? $page->content_en : $page->content)) !!}
                            </div>
                            @endif

                            <div class="vb-page-footer">{{ $index + 1 }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Login Modal (if required) --}}
@if($requiresLoginModal)
    @include('partials.login_modal', [
        'previewImage' => $loginModalPreview,
        'roomName' => $loginModalRoomName
    ])
@endif

@endsection

@push('scripts')
<script type="module">
    import { PageFlip } from 'page-flip';

    document.addEventListener('DOMContentLoaded', function() {
        const pageFlip = new PageFlip(
            document.getElementById("demoBookExample"),
            {
                width: 550,
                height: 733,
                size: "stretch",
                minWidth: 315,
                maxWidth: 1000,
                minHeight: 420,
                maxHeight: 1350,
                maxShadowOpacity: 0.5,
                showCover: true,
                mobileScrollSupport: false
            }
        );

        // Load pages
        pageFlip.loadFromHTML(document.querySelectorAll(".vb-page"));

        document.getElementById("pageTotal").innerText = pageFlip.getPageCount();

        document.getElementById("btnPrev").addEventListener("click", function() {
            pageFlip.flipPrev();
        });

        document.getElementById("btnNext").addEventListener("click", function() {
            pageFlip.flipNext();
        });

        // Update current page
        pageFlip.on("flip", function(e) {
            document.getElementById("pageCurrent").innerText = e.data + 1;
        });

        // Update state
        pageFlip.on("changeState", function(e) {
            document.getElementById("pageState").innerText = e.data;
        });
    });
</script>
@endpush
