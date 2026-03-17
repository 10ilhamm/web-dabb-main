@extends('layouts.guest')

@section('title', ($locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title) . ' — ' . $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/virtual_slideshow.css') }}">
<style>
    /* Back button for slideshow view */
    .vss-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        background: rgba(255,255,255,0.15);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        text-decoration: none;
        font-size: 0.875rem;
        margin-bottom: 1rem;
        transition: background 0.2s;
    }
    .vss-back-btn:hover {
        background: rgba(255,255,255,0.25);
        color: white;
    }
</style>
@endpush

@section('content')

@php
    $locale = app()->getLocale();

    $heroSlide = $slides->firstWhere('slide_type', 'hero');
    $contentSlides = $slides->where('slide_type', '!=', 'hero')->values();

    function vssYouTubeEmbed($url) {
        if (!$url) return null;
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
            }
        }
        return $url; // direct MP4 or other
    }
@endphp

{{-- Scroll Progress Bar --}}
<div class="vsshow-progress-bar" id="vss-progress"></div>

{{-- Breadcrumb with back button --}}
<div class="vsshow-breadcrumb" style="background: #f8fafc; padding: 0.75rem 0;">
    <div class="vsshow-container" style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <a href="{{ url('/') }}">Beranda</a>
            @if($feature->parent)
                <span class="sep">/</span>
                <a href="{{ url($feature->parent->path ?? '#') }}">
                    {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
                </a>
            @endif
            <span class="sep">/</span>
            <a href="{{ url($feature->path) }}">{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</a>
            <span class="sep">/</span>
            <span>{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</span>
        </div>
    </div>
</div>

{{-- ======== HERO SECTION ======== --}}
@if($heroSlide)
<section class="vsshow-hero" style="{{ $heroSlide->bg_color && $heroSlide->bg_color !== '#ffffff' ? 'background: linear-gradient(135deg, '.e($heroSlide->bg_color).' 0%, #174E93 60%, #2563EB 100%);' : '' }}">
    <div id="vss-particles" class="vsshow-hero-particles"></div>

    @if($heroSlide->images && count($heroSlide->images) > 0)
    <div style="position:absolute;inset:0;z-index:0;">
        <img src="{{ asset('storage/'.$heroSlide->images[0]) }}"
            alt="{{ $heroSlide->title }}"
            style="width:100%;height:100%;object-fit:cover;opacity:0.25;">
    </div>
    @endif

    <div class="vsshow-hero-content vsshow-anim vsshow-anim-up" data-delay="100">
        <div class="vsshow-hero-badge">
            {{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}
        </div>
        @if($heroSlide->title)
        <h1 class="vsshow-hero-title">
            {{ $locale === 'en' && $heroSlide->title_en ? $heroSlide->title_en : $heroSlide->title }}
        </h1>
        @else
        <h1 class="vsshow-hero-title">
            {{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}
        </h1>
        @endif
        @if($heroSlide->subtitle)
        <p class="vsshow-hero-subtitle">
            {{ $locale === 'en' && $heroSlide->subtitle_en ? $heroSlide->subtitle_en : $heroSlide->subtitle }}
        </p>
        @endif
        @if($heroSlide->description)
        <p class="vsshow-hero-subtitle" style="font-size:1rem;opacity:0.7;">
            {!! $locale === 'en' && $heroSlide->description_en ? $heroSlide->description_en : $heroSlide->description !!}
        </p>
        @endif
    </div>

    <div class="vsshow-hero-scroll-hint">
        <div class="vsshow-hero-scroll-line"></div>
        Scroll
    </div>
</section>
@else
{{-- Default Hero when no hero slide --}}
<section class="vsshow-hero">
    <div id="vss-particles" class="vsshow-hero-particles"></div>
    <div class="vsshow-hero-content vsshow-anim vsshow-anim-up" data-delay="100">
        <div class="vsshow-hero-badge">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
        <h1 class="vsshow-hero-title">
            {{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}
        </h1>
        @if($selectedPage->description)
        <p class="vsshow-hero-subtitle">{!! Str::limit(strip_tags($locale === 'en' && $selectedPage->description_en ? $selectedPage->description_en : $selectedPage->description), 200) !!}</p>
        @endif
    </div>
    <div class="vsshow-hero-scroll-hint">
        <div class="vsshow-hero-scroll-line"></div>
        Scroll
    </div>
</section>
@endif

{{-- ======== CONTENT SLIDES ======== --}}
@foreach($contentSlides as $slideIndex => $slide)
@php
    $title = $locale === 'en' && $slide->title_en ? $slide->title_en : $slide->title;
    $subtitle = $locale === 'en' && $slide->subtitle_en ? $slide->subtitle_en : $slide->subtitle;
    $desc = $locale === 'en' && $slide->description_en ? $slide->description_en : $slide->description;
    $images = $slide->images ?? [];
    $popup = $slide->info_popup ?? [];
    $bgStyle = ($slide->bg_color && $slide->bg_color !== '#ffffff') ? "background-color: {$slide->bg_color};" : '';
    $animDir = $slideIndex % 2 === 0 ? 'vsshow-anim-left' : 'vsshow-anim-right';
    $delay = $slideIndex * 80;
    $embedUrl = vssYouTubeEmbed($slide->video_url);
    $isYoutube = $slide->video_url && str_contains($slide->video_url, 'youtube') || str_contains((string)$slide->video_url, 'youtu.be');
@endphp

<section class="vsshow-section" style="{{ $bgStyle }}">
    <div class="vsshow-container">

        {{-- TEXT only --}}
        @if($slide->slide_type === 'text')
        <div class="vsshow-text-section vsshow-anim vsshow-anim-up" data-delay="{{ $delay }}">
            @if($title)
                <div class="vsshow-section-tag">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title">{{ $title }}</h2>
                <div class="vsshow-divider"></div>
            @endif
            @if($subtitle)
                <p class="vsshow-section-subtitle">{{ $subtitle }}</p>
            @endif
            @if($desc)
                <div class="vsshow-section-desc">{!! $desc !!}</div>
            @endif
        </div>

        {{-- CAROUSEL only --}}
        @elseif($slide->slide_type === 'carousel')
        <div class="vsshow-anim vsshow-anim-up" data-delay="{{ $delay }}">
            @if($title)
            <div class="vsshow-text-section" style="margin-bottom:2.5rem;">
                <div class="vsshow-section-tag">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title">{{ $title }}</h2>
                <div class="vsshow-divider"></div>
                @if($subtitle)<p class="vsshow-section-subtitle">{{ $subtitle }}</p>@endif
            </div>
            @endif

            @if(count($images) > 0)
            <div class="vsshow-carousel">
                <div class="vsshow-carousel-track">
                    @foreach($images as $imgIdx => $imgPath)
                    <div class="vsshow-carousel-slide">
                        <img src="{{ asset('storage/'.$imgPath) }}" alt="{{ $title }} — gambar {{ $imgIdx+1 }}" loading="lazy">
                        @if(!empty($popup[$imgIdx]) || !empty($popup[(string)$imgIdx]))
                        <button class="vsshow-info-btn"
                            data-popup="{{ $popup[$imgIdx] ?? $popup[(string)$imgIdx] ?? '' }}"
                            data-img-src="{{ asset('storage/'.$imgPath) }}"
                            title="Info">?</button>
                        @endif
                    </div>
                    @endforeach
                </div>

                @if(count($images) > 1)
                <button class="vsshow-carousel-btn prev" aria-label="Previous">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button class="vsshow-carousel-btn next" aria-label="Next">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                @endif

                <div class="vsshow-carousel-dots">
                    @foreach($images as $imgIdx => $_)
                    <span class="vsshow-dot {{ $imgIdx === 0 ? 'active' : '' }}" data-idx="{{ $imgIdx }}"></span>
                    @endforeach
                </div>
            </div>

            @if($desc)
            <p class="vsshow-section-desc" style="text-align:center;margin-top:2rem;">{!! $desc !!}</p>
            @endif
            @endif
        </div>

        {{-- VIDEO --}}
        @elseif($slide->slide_type === 'video')
        <div class="vsshow-anim vsshow-anim-up" data-delay="{{ $delay }}">
            @if($title)
            <div class="vsshow-text-section" style="margin-bottom:2.5rem;">
                <div class="vsshow-section-tag">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title">{{ $title }}</h2>
                <div class="vsshow-divider"></div>
                @if($subtitle)<p class="vsshow-section-subtitle">{{ $subtitle }}</p>@endif
                @if($desc)<div class="vsshow-section-desc">{!! $desc !!}</div>@endif
            </div>
            @endif

            @if($slide->video_url)
            <div class="vsshow-video-wrap">
                @if(!empty($popup['video']))
                <button class="vsshow-info-btn vsshow-video-info-btn"
                    data-popup="{{ $popup['video'] }}"
                    title="Info Video">?</button>
                @endif

                @if($isYoutube || str_contains((string)$slide->video_url, 'youtube') || str_contains((string)$slide->video_url, 'youtu.be') || str_contains((string)$slide->video_url, 'embed'))
                <div class="vsshow-video-iframe-wrap" data-src="{{ $embedUrl }}">
                    <iframe data-src="{{ $embedUrl }}" allowfullscreen allow="autoplay; encrypted-media"
                        title="{{ $title ?? 'Video' }}"></iframe>
                </div>
                @else
                <video controls style="width:100%;max-height:480px;display:block;background:#000;">
                    <source src="{{ $slide->video_url }}" type="video/mp4">
                    Browser Anda tidak mendukung video.
                </video>
                @endif
            </div>
            @endif
        </div>

        {{-- TEXT + CAROUSEL --}}
        @elseif($slide->slide_type === 'text_carousel')
        <div class="vsshow-split {{ $slide->layout === 'right' ? 'vsshow-split-right' : '' }}">
            {{-- Text --}}
            <div class="vsshow-split-text vsshow-anim {{ $slide->layout === 'right' ? 'vsshow-anim-right' : 'vsshow-anim-left' }}" data-delay="{{ $delay }}">
                @if($title)
                <div class="vsshow-section-tag">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title" style="text-align:left;">{{ $title }}</h2>
                <div class="vsshow-divider"></div>
                @endif
                @if($subtitle)
                <p class="vsshow-section-subtitle" style="text-align:left;">{{ $subtitle }}</p>
                @endif
                @if($desc)
                <div class="vsshow-section-desc" style="text-align:left;">{!! $desc !!}</div>
                @endif
            </div>

            {{-- Carousel --}}
            <div class="vsshow-anim {{ $slide->layout === 'right' ? 'vsshow-anim-left' : 'vsshow-anim-right' }}" data-delay="{{ $delay + 100 }}">
                @if(count($images) > 0)
                <div class="vsshow-carousel">
                    <div class="vsshow-carousel-track">
                        @foreach($images as $imgIdx => $imgPath)
                        <div class="vsshow-carousel-slide">
                            <img src="{{ asset('storage/'.$imgPath) }}" alt="{{ $title }} — gambar {{ $imgIdx+1 }}" loading="lazy">
                            @if(!empty($popup[$imgIdx]) || !empty($popup[(string)$imgIdx]))
                            <button class="vsshow-info-btn"
                                data-popup="{{ $popup[$imgIdx] ?? $popup[(string)$imgIdx] ?? '' }}"
                                data-img-src="{{ asset('storage/'.$imgPath) }}"
                                title="Info">?</button>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    @if(count($images) > 1)
                    <button class="vsshow-carousel-btn prev" aria-label="Previous">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="vsshow-carousel-btn next" aria-label="Next">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    @endif

                    <div class="vsshow-carousel-dots">
                        @foreach($images as $imgIdx => $_)
                        <span class="vsshow-dot {{ $imgIdx === 0 ? 'active' : '' }}" data-idx="{{ $imgIdx }}"></span>
                        @endforeach
                    </div>
                </div>
                @elseif($slide->video_url)
                <div class="vsshow-video-wrap">
                    @if(!empty($popup['video']))
                    <button class="vsshow-info-btn vsshow-video-info-btn"
                        data-popup="{{ $popup['video'] }}" title="Info Video">?</button>
                    @endif
                    <div class="vsshow-video-iframe-wrap" data-src="{{ $embedUrl }}">
                        <iframe data-src="{{ $embedUrl }}" allowfullscreen allow="autoplay; encrypted-media"
                            title="{{ $title ?? 'Video' }}"></iframe>
                    </div>
                </div>
                @endif
            </div>
        </div>{{-- end split --}}
        @endif

    </div>{{-- end container --}}
</section>
@endforeach

{{-- Empty state --}}
@if($slides->isEmpty())
<section class="vsshow-section" style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
    <div class="vsshow-text-section">
        <div style="font-size:4rem;margin-bottom:1rem;">🎞</div>
        <h2 class="vsshow-section-title" style="color:#94a3b8;">Konten sedang disiapkan</h2>
        <p class="vsshow-section-desc">Halaman ini belum memiliki slide. Silakan kembali lagi nanti.</p>
    </div>
</section>
@endif

{{-- ======== INFO POPUP MODAL ======== --}}
<div id="vss-popup-overlay" class="vsshow-popup-overlay"></div>
<div id="vss-popup-card" class="vsshow-popup-card" role="dialog" aria-modal="true" aria-labelledby="vss-popup-title">
    <div class="vsshow-popup-header">
        <div class="vsshow-popup-icon">?</div>
        <button id="vss-popup-close" class="vsshow-popup-close" aria-label="Tutup">✕</button>
    </div>
    <img id="vss-popup-img" class="vsshow-popup-img" src="" alt="" style="display:none;">
    <div id="vss-popup-body" class="vsshow-popup-body"></div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/virtual_slideshow.js') }}"></script>
@endpush
