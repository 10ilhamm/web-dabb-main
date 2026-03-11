@extends('layouts.app')

@section('breadcrumb_parent', 'CMS / ' . __('cms.features.title'))
@section('breadcrumb_active', 'Beranda')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.home.title') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.home.desc') }}</p>
        </div>
        <a href="{{ route('home') }}" target="_blank"
            class="ml-auto inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#174E93] bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            {{ __('cms.home.view_page') }}
        </a>
    </div>

    {{-- ===== BAHASA INDONESIA ONLY, ENGLISH TAB REMOVED AS PER USER REQUEST ===== --}}
    <div>
        <form action="{{ route('cms.home.update') }}" method="POST" class="flex flex-col gap-0">
            @csrf
            <input type="hidden" name="locale" value="id">

            {{-- Hero Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#174E93] flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4l-1 16h12L17 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.hero.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.hero.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.hero.hero_title') }}</label>
                        <input type="text" name="hero_title" value="{{ $idContent['hero_title'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.hero.hero_cta') }}</label>
                        <input type="text" name="hero_cta" value="{{ $idContent['hero_cta'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>
            </div>

            {{-- Feature Strip --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.feature_strip.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.feature_strip.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.left') }}</label>
                        <textarea name="feature_strip[left]" rows="3"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ $idContent['feature_strip']['left'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.middle') }}</label>
                        <input type="text" name="feature_strip[middle]" value="{{ $idContent['feature_strip']['middle'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.right_button') }}</label>
                        <input type="text" name="feature_strip[right_button]" value="{{ $idContent['feature_strip']['right_button'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.right_text') }}</label>
                        <textarea name="feature_strip[right_text]" rows="3"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ $idContent['feature_strip']['right_text'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.info.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.info.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.section') }}</label>
                        <input type="text" name="sections[info_title]" value="{{ $idContent['sections']['info_title'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.paragraph1') }}</label>
                        <textarea name="sections[info_1]" rows="4"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y">{{ $idContent['sections']['info_1'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.paragraph2') }}</label>
                        <textarea name="sections[info_2]" rows="4"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y">{{ $idContent['sections']['info_2'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Activities Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.activities.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.activities.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.activities.section') }}</label>
                        <input type="text" name="sections[activities]" value="{{ $idContent['sections']['activities'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php $actColors = ['#D06767','#3598DB','#89DB51','#000000','#DB420F','#E660D4']; @endphp
                        @foreach(($idContent['activity_items'] ?? []) as $i => $item)
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                                style="background: {{ $actColors[$i] ?? '#999' }}">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</div>
                            <input type="text" name="activity_items[{{ $i }}]" value="{{ $item }}"
                                class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Section Titles --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.section_titles.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.section_titles.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $sectionLabels = [
                            'related'    => __('cms.home.section_titles.related'),
                            'gallery'    => __('cms.home.section_titles.gallery'),
                            'stats'      => __('cms.home.section_titles.stats'),
                            'youtube'    => __('cms.home.section_titles.youtube'),
                            'instagram'  => __('cms.home.section_titles.instagram'),
                        ];
                    @endphp
                    @foreach($sectionLabels as $key => $label)
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ $label }}</label>
                        <input type="text" name="sections[{{ $key }}]" value="{{ $idContent['sections'][$key] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.stats.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.stats.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.stats.total') }}</label>
                        <input type="text" name="stats[total]" value="{{ $idContent['stats']['total'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.stats.today') }}</label>
                        <input type="text" name="stats[today]" value="{{ $idContent['stats']['today'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>
            </div>

            {{-- YouTube Videos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.youtube.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.youtube.desc') }}</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $youtubeIds = $idContent['youtube_ids'] ?? ['F2NhNTiNxoY','kasMsnf9Cys','LgdR55MPAnU','NC9_ugD6vxo','F2NhNTiNxoY'];
                        @endphp
                        @foreach($youtubeIds as $yi => $vid)
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.youtube.video_label', ['number' => $yi + 1]) }}</label>
                            <div class="flex gap-2">
                                <input type="text" name="youtube_ids[{{ $yi }}]" value="{{ $vid }}"
                                    placeholder="{{ __('cms.home.youtube.placeholder') }}"
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                @if($vid)
                                <a href="https://youtube.com/watch?v={{ $vid }}" target="_blank"
                                    class="inline-flex items-center justify-center w-9 h-9 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg transition-colors border border-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-3">{!! __('cms.home.youtube.help') !!}</p>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-end gap-3 pb-4">
                <a href="{{ route('cms.features.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition-colors">
                    {{ __('cms.common.back') }}
                </a>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors shadow-sm">
                    {{ __('cms.common.save_content') }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
