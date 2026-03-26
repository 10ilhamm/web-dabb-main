<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeaturePage;
use App\Models\VirtualSlideshowPage;
use App\Models\VirtualSlideshowSlide;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VirtualSlideshowController extends Controller
{
    /**
     * Show pages table for this slideshow feature
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->slideshowPages()->withCount('slideshowSlides')->with('slideshowSlides')->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.index', compact('feature', 'pages'));
    }

    /**
     * Show slides for a specific page
     */
    public function slidesIndex(Feature $feature, $pageId)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        $feature->load('parent');
        $page->load('slideshowSlides');
        $slides = $page->slideshowSlides()->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.pages.slides_index', compact('feature', 'page', 'slides'));
    }

    /**
     * Create new slide (legacy - for slides without page)
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->slideshowPages()->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.create', compact('feature', 'pages'));
    }

    /**
     * Store new slide (legacy)
     */
    public function store(Request $request, Feature $feature, TranslationService $translationService)
    {
        return $this->storeSlideData($request, $feature, null, $translationService);
    }

    /**
     * Create slide for specific page
     */
    public function createSlide(Feature $feature, $pageId)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        $feature->load('parent');

        $hasHeroSlide = VirtualSlideshowSlide::where('feature_page_id', $page->id)
            ->where('slide_type', 'hero')
            ->exists();

        return view('cms.features.virtual_slideshow.pages.create', compact('feature', 'page', 'hasHeroSlide'));
    }

    /**
     * Store slide for specific page
     */
    public function storeSlide(Request $request, Feature $feature, $pageId, TranslationService $translationService)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        return $this->storeSlideData($request, $feature, $page, $translationService);
    }

    /**
     * Build caption value based on mode (single string or multi Q&A object)
     */
    private function buildCaptionValue(string $mode, ?string $singleCaption, ?array $qaItems): mixed
    {
        if ($mode === 'multi' && !empty($qaItems)) {
            $items = [];
            foreach ($qaItems as $qa) {
                $q = trim($qa['question'] ?? '');
                $a = trim($qa['answer'] ?? '');
                if ($q !== '' || $a !== '') {
                    $items[] = ['question' => $q, 'answer' => $a];
                }
            }
            if (!empty($items)) {
                return ['type' => 'multi', 'items' => $items];
            }
        }
        // Fall back to single caption
        return !empty($singleCaption) ? $singleCaption : null;
    }

    /**
     * Shared method to store slide data
     */
    private function storeSlideData(Request $request, Feature $feature, ?VirtualSlideshowPage $page, TranslationService $translationService)
    {
        $validated = $request->validate([
            'feature_page_id' => 'nullable|exists:feature_pages,id',
            'slide_type'  => 'required|in:hero,text,carousel,video,text_carousel',
            'carousel_media_type' => 'nullable|in:images,videos',
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'layout'      => 'required|in:left,right,center',
            'bg_color'   => 'nullable|string|max:20',
            'order'       => 'required|integer|min:0',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp,gif',
            'image_urls'  => 'nullable|array',
            'image_urls.*'=> 'nullable|string',
            'carousel_videos' => 'nullable|array',
            'carousel_videos.*' => 'file',
            'carousel_video_urls' => 'nullable|array',
            'carousel_video_urls.*' => 'nullable|string',
            'video_url'   => 'nullable|string|max:500',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg',
            'info_popup_images'   => 'nullable|array',
            'info_popup_images.*' => 'nullable|string',
            'info_popup_new_images' => 'nullable|array',
            'info_popup_new_images.*' => 'nullable|string',
            'info_popup_carousel_videos' => 'nullable|array',
            'info_popup_carousel_videos.*' => 'nullable|string',
            'info_popup_video'    => 'nullable|string',
            'info_popup_video_url' => 'nullable|string',
            'info_popup_mode_images' => 'nullable|array',
            'info_popup_mode_images.*' => 'nullable|in:single,multi',
            'info_popup_qa_images' => 'nullable|array',
            'info_popup_mode_video' => 'nullable|in:single,multi',
            'info_popup_qa_video' => 'nullable|array',
            'info_popup_mode_video_url' => 'nullable|in:single,multi',
            'info_popup_qa_video_url' => 'nullable|array',
            'info_popup_mode_carousel_videos' => 'nullable|array',
            'info_popup_mode_carousel_videos.*' => 'nullable|in:single,multi',
            'info_popup_qa_carousel_videos' => 'nullable|array',
            'unified_video_order' => 'nullable',
        ]);

        // Determine which data to store based on slide type
        $slideType = $validated['slide_type'];

        // Server-side backup: prevent duplicate hero slides per page
        if ($slideType === 'hero' && $page) {
            $existingHero = VirtualSlideshowSlide::where('feature_page_id', $page->id)
                ->where('slide_type', 'hero')
                ->exists();
            if ($existingHero) {
                return back()->withErrors(['slide_type' => 'Halaman ini sudah memiliki slide Hero.'])->withInput();
            }
        }

        $imageTypes = ['hero', 'carousel'];
        $videoTypes = ['video'];
        $carouselVideoTypes = ['text_carousel'];
        // text_carousel can use either images or videos (based on carousel_media_type toggle)
        $textCarouselTypes = ['text_carousel'];
        $usesImagesForCarousel = $slideType === 'text_carousel' &&
                                  isset($validated['carousel_media_type']) &&
                                  $validated['carousel_media_type'] === 'images';

        $useImages = in_array($slideType, $imageTypes);
        $useVideo = in_array($slideType, $videoTypes);
        $useCarouselVideo = in_array($slideType, $carouselVideoTypes) && !$usesImagesForCarousel;
        $useCarouselImages = $usesImagesForCarousel;

        // Upload images (for hero, carousel, or text_carousel with images selected)
        $imagePaths = [];
        if (($useImages || $useCarouselImages) && $request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $imagePaths[] = $img->store('features/slideshow', 'public');
            }
        }

        // Process image URLs (filter empty values)
        $imageUrls = [];
        if (($useImages || $useCarouselImages) && !empty($validated['image_urls'])) {
            $imageUrls = array_values(array_filter(array_map('trim', $validated['image_urls']), function($url) {
                return !empty($url);
            }));
        }

        // Upload carousel video files (only for text_carousel type)
        $carouselVideoPaths = [];
        if ($useCarouselVideo && $request->hasFile('carousel_videos')) {
            foreach ($request->file('carousel_videos') as $video) {
                $carouselVideoPaths[] = $video->store('features/slideshow/videos', 'public');
            }
        }

        // Get unified video order from form
        $unifiedOrder = [];
        $unifiedInput = $request->input('unified_video_order');
        if ($unifiedInput) {
            $decodedOrder = is_array($unifiedInput) ? $unifiedInput : json_decode($unifiedInput, true);
            $unifiedOrder = is_array($decodedOrder) ? $decodedOrder : [];
        }

        // Process carousel videos using unified order
        $orderedUrls = [];
        $orderedUploads = [];
        
        if ($useCarouselVideo) {
            if (!empty($unifiedOrder)) {
                // Process based on unified order
                foreach ($unifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $url = trim($item['urlValue'] ?? '');
                        if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                            $orderedUrls[] = $url;
                        }
                    } elseif ($type === 'newUpload') {
                        // Use array_shift to take new uploads in the order they appear in unifiedOrder
                        if (!empty($carouselVideoPaths)) {
                            $orderedUploads[] = array_shift($carouselVideoPaths);
                        }
                    }
                }
            } else {
                // Fallback: use form order
                if (!empty($validated['carousel_video_urls'])) {
                    $orderedUrls = array_values(array_filter(array_map('trim', $validated['carousel_video_urls']), function($url) {
                        return !empty($url);
                    }));
                }
                $orderedUploads = $carouselVideoPaths;
            }
        }

        // Save original order for caption mapping (before normalization)
        $originalUnifiedOrder = $unifiedOrder;

        // Normalize unifiedOrder for store: convert newUpload entries to upload entries
        if ($useCarouselVideo && !empty($unifiedOrder)) {
            $normalizedOrder = [];
            $newIdx = 0;
            foreach ($unifiedOrder as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'newUpload') {
                    $uploadPath = $orderedUploads[$newIdx] ?? null;
                    $normalizedOrder[] = [
                        'type' => 'upload',
                        'uploadPath' => $uploadPath,
                        'uploadIndex' => count(array_filter($normalizedOrder, fn($i) => $i['type'] === 'upload')),
                    ];
                    $newIdx++;
                } else {
                    $normalizedOrder[] = $item;
                }
            }
            $unifiedOrder = $normalizedOrder;
        }

        // Upload video file (only for video type)
        $videoFilePath = null;
        if ($useCarouselVideo && !empty($orderedUploads)) {
            // For text_carousel, store uploaded videos as JSON array in order
            $videoFilePath = json_encode(array_values($orderedUploads));
        } elseif ($useVideo && $request->hasFile('video_file')) {
            $videoFilePath = $request->file('video_file')->store('features/slideshow/videos', 'public');
        }

        // Get video URL (single video only)
        $primaryVideoUrl = null;
        if ($useVideo && !empty($validated['video_url'])) {
            $primaryVideoUrl = trim($validated['video_url']);
        }

        // Build info_popup array
        $infoPopup = [];
        $captionModes = $validated['info_popup_mode_images'] ?? [];
        $captionQaData = $request->input('info_popup_qa_images', []);

        if (($useImages || $useCarouselImages)) {
            // For new slides, use info_popup_images directly (since no existing images)
            if (!empty($validated['info_popup_images'])) {
                foreach ($validated['info_popup_images'] as $idx => $caption) {
                    $mode = $captionModes[$idx] ?? 'single';
                    $qaItems = $captionQaData[$idx] ?? [];
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value !== null) {
                        $infoPopup[(string)$idx] = $value;
                    }
                }
            }
            // Also support info_popup_new_images for consistency
            if (!empty($validated['info_popup_new_images'])) {
                $startIdx = !empty($validated['info_popup_images']) ? count($validated['info_popup_images']) : 0;
                $newImageModes = $request->input('info_popup_mode_new_images', []);
                $newImageQaData = $request->input('info_popup_qa_new_images', []);
                foreach ($validated['info_popup_new_images'] as $idx => $caption) {
                    $actualIdx = $startIdx + $idx;
                    $mode = $newImageModes[$idx] ?? ($captionModes[$actualIdx] ?? 'single');
                    $qaItems = $newImageQaData[$idx] ?? ($captionQaData[$actualIdx] ?? []);
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value !== null) {
                        $infoPopup[(string)$actualIdx] = $value;
                    }
                }
            }
        }
        if ($useVideo) {
            $videoMode = $validated['info_popup_mode_video'] ?? 'single';
            $videoQaItems = $request->input('info_popup_qa_video', []);
            $videoValue = $this->buildCaptionValue($videoMode, $validated['info_popup_video'] ?? null, $videoQaItems);
            if ($videoValue !== null) {
                $infoPopup['video'] = $videoValue;
            }
            // Video URL caption
            $videoUrlMode = $validated['info_popup_mode_video_url'] ?? 'single';
            $videoUrlQaItems = $request->input('info_popup_qa_video_url', []);
            $videoUrlValue = $this->buildCaptionValue($videoUrlMode, $validated['info_popup_video_url'] ?? null, $videoUrlQaItems);
            if ($videoUrlValue !== null) {
                $infoPopup['video_url'] = $videoUrlValue;
            }
        }
        if ($useCarouselVideo) {
            // Store normalized order for reconstruction on load
            $infoPopup['carousel_video_order'] = $unifiedOrder;

            if (!empty($validated['info_popup_carousel_videos'])) {
                $infoPopup['carousel_videos'] = [];

                // Build mapping using ORIGINAL order (before normalization)
                // because form sends keys like newUpload_X
                $captionToStorage = [];
                $urlStorageIdx = 0;
                $uploadStorageIdx = 0;

                foreach ($originalUnifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $urlIdx = $item['urlIndex'] ?? 0;
                        $captionToStorage['url_' . $urlIdx] = ['type' => 'url', 'storageIdx' => $urlStorageIdx];
                        $urlStorageIdx++;
                    } elseif ($type === 'newUpload') {
                        $newUploadIdx = $item['newUploadIndex'] ?? 0;
                        $captionToStorage['newUpload_' . $newUploadIdx] = ['type' => 'upload', 'storageIdx' => $uploadStorageIdx];
                        $uploadStorageIdx++;
                    }
                }

                // Process captions: remap newUpload_X keys to sequential upload_X keys
                $carouselVideoModes = $validated['info_popup_mode_carousel_videos'] ?? [];
                $carouselVideoQaData = $request->input('info_popup_qa_carousel_videos', []);

                foreach ($validated['info_popup_carousel_videos'] as $key => $caption) {
                    $mode = $carouselVideoModes[$key] ?? 'single';
                    $qaItems = $carouselVideoQaData[$key] ?? [];
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value === null) continue;

                    if (str_starts_with($key, 'newUpload_')) {
                        $storageKey = 'upload_' . ($captionToStorage[$key]['storageIdx'] ?? 0);
                        $infoPopup['carousel_videos'][$storageKey] = $value;
                    } else {
                        $infoPopup['carousel_videos'][$key] = $value;
                    }
                }
            }
        }

        // Determine feature_page_id
        $featurePageId = $page ? $page->id : ($validated['feature_page_id'] ?? null);

        // Determine which media to store based on slide type
        $storeImages = ($useImages || $useCarouselImages) ? ($imagePaths ?: null) : null;
        $storeImageUrls = ($useImages || $useCarouselImages) ? ($imageUrls ?: null) : null;
        $storeCarouselVideos = $useCarouselVideo ? (!empty($orderedUploads) ? $orderedUploads : null) : null;
        $storeCarouselVideoUrls = $useCarouselVideo ? (!empty($orderedUrls) ? $orderedUrls : null) : null;

        // Hero: hanya boleh upload ATAU URL, tidak boleh keduanya
        if (($useImages || $useCarouselImages) && $slideType === 'hero') {
            $hasUploadedImages = !empty($imagePaths);
            $hasUrlImages = !empty($imageUrls);

            if ($hasUploadedImages && $hasUrlImages) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL.'], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image_conflict' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL, tidak boleh keduanya sekaligus.']);
            }
        }

        VirtualSlideshowSlide::create([
            'feature_id'      => $feature->id,
            'feature_page_id' => $featurePageId,
            'slide_type'     => $validated['slide_type'],
            'title'          => $validated['title'] ?? null,
            'title_en'       => !empty($validated['title']) ? $translationService->translate($validated['title']) : null,
            'subtitle'       => $validated['subtitle'] ?? null,
            'subtitle_en'    => !empty($validated['subtitle']) ? $translationService->translate($validated['subtitle']) : null,
            'description'    => $validated['description'] ?? null,
            'description_en' => !empty($validated['description']) ? $translationService->translate($validated['description']) : null,
            'images'         => $storeImages,
            'image_urls'     => $storeImageUrls,
            'video_url'      => $primaryVideoUrl,
            'video_file'     => $videoFilePath,
            'carousel_video_urls' => $storeCarouselVideoUrls,
            'layout'         => $validated['layout'],
            'bg_color'      => $validated['bg_color'] ?? null,
            'info_popup'    => $infoPopup ?: null,
            'order'          => $validated['order'],
        ]);

        // Redirect based on context
        if ($page) {
            return redirect()->route('cms.features.slideshow.pages.slides.index', [$feature, $page])
                ->with('success', 'Slide berhasil ditambahkan.');
        }

        return redirect()->route('cms.features.slideshow.index', $feature)
            ->with('success', 'Slide berhasil ditambahkan.');
    }

    /**
     * Edit slide (legacy)
     */
    public function edit(Feature $feature, VirtualSlideshowSlide $slide)
    {
        $feature->load('parent');
        $pages = $feature->pages()->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.edit', compact('feature', 'slide', 'pages'));
    }

    /**
     * Update slide (legacy)
     */
    public function update(Request $request, Feature $feature, VirtualSlideshowSlide $slide, TranslationService $translationService)
    {
        return $this->updateSlideData($request, $feature, $slide, $translationService);
    }

    /**
     * Edit slide for specific page
     */
    public function editSlide(Feature $feature, $pageId, VirtualSlideshowSlide $slide)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        $feature->load('parent');
        $hasHeroSlide = VirtualSlideshowSlide::where('feature_page_id', $page->id)
            ->where('slide_type', 'hero')
            ->where('id', '!=', $slide->id)
            ->exists();
        return view('cms.features.virtual_slideshow.pages.edit', compact('feature', 'page', 'slide', 'hasHeroSlide'));
    }

    /**
     * Update slide for specific page
     */
    public function updateSlide(Request $request, Feature $feature, $pageId, VirtualSlideshowSlide $slide, TranslationService $translationService)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        return $this->updateSlideData($request, $feature, $slide, $translationService, $page);
    }

     /**
     * Shared method to update slide data
     */
    private function updateSlideData(Request $request, Feature $feature, VirtualSlideshowSlide $slide, TranslationService $translationService, ?VirtualSlideshowPage $page = null)
    {
        $validated = $request->validate([
            'feature_page_id' => 'nullable|exists:feature_pages,id',
            'slide_type'  => 'required|in:hero,text,carousel,video,text_carousel',
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'layout'      => 'required|in:left,right,center',
            'bg_color'   => 'nullable|string|max:20',
            'order'       => 'required|integer|min:0',
            'images'      => 'nullable|array',
            'images.*'    => 'nullable|file',
            'existing_images'     => 'nullable|array',
            'existing_images.*'   => 'string',
            'deleted_existing_images' => 'nullable|array',
            'deleted_existing_images.*' => 'string',
            'image_urls'  => 'nullable|array',
            'image_urls.*'=> 'nullable|string',
            'carousel_videos' => 'nullable|array',
            'carousel_videos.*' => 'file',
            'carousel_video_urls' => 'nullable|array',
            'carousel_video_urls.*' => 'nullable|string',
            'existing_carousel_videos' => 'nullable',
            'unified_video_order' => 'nullable',
            'carousel_media_type' => 'nullable|in:images,videos',
            'video_method' => 'nullable|in:url,upload',
            'video_url'   => 'nullable|string|max:500',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg',
            'delete_existing_video' => 'nullable|in:0,1',
            'clear_existing_url' => 'nullable|in:0,1',
            'info_popup_images'   => 'nullable|array',
            'info_popup_images.*' => 'nullable|string',
            'info_popup_new_images' => 'nullable|array',
            'info_popup_new_images.*' => 'nullable|string',
            'info_popup_carousel_videos' => 'nullable|array',
            'info_popup_carousel_videos.*' => 'nullable|string',
            'info_popup_video'    => 'nullable|string',
            'info_popup_video_url' => 'nullable|string',
            'info_popup_mode_images' => 'nullable|array',
            'info_popup_mode_images.*' => 'nullable|in:single,multi',
            'info_popup_qa_images' => 'nullable|array',
            'info_popup_mode_video' => 'nullable|in:single,multi',
            'info_popup_qa_video' => 'nullable|array',
            'info_popup_mode_video_url' => 'nullable|in:single,multi',
            'info_popup_qa_video_url' => 'nullable|array',
            'info_popup_mode_carousel_videos' => 'nullable|array',
            'info_popup_mode_carousel_videos.*' => 'nullable|in:single,multi',
            'info_popup_qa_carousel_videos' => 'nullable|array',
        ]);

        // Server-side: prevent duplicate hero slides per page
        $slideType = $validated['slide_type'];
        if ($slideType === 'hero' && $page) {
            $existingHero = VirtualSlideshowSlide::where('feature_page_id', $page->id)
                ->where('slide_type', 'hero')
                ->where('id', '!=', $slide->id)
                ->exists();
            if ($existingHero) {
                return back()->withErrors(['slide_type' => 'Halaman ini sudah memiliki slide Hero.'])->withInput();
            }
        }

        // Determine which data to keep based on slide type
        $imageTypes = ['hero', 'carousel'];
        $videoTypes = ['video'];
        $carouselVideoTypes = ['text_carousel'];
        // text_carousel can use either images or videos (based on carousel_media_type toggle)
        $usesImagesForCarousel = $slideType === 'text_carousel' &&
                                  isset($validated['carousel_media_type']) &&
                                  $validated['carousel_media_type'] === 'images';

        $useImages = in_array($slideType, $imageTypes);
        $useVideo = in_array($slideType, $videoTypes);
        $useCarouselVideo = in_array($slideType, $carouselVideoTypes) && !$usesImagesForCarousel;
        $useCarouselImages = $usesImagesForCarousel;

        // Get existing images from form (only enabled inputs will be submitted)
        $existingImages = [];
        if (($useImages || $useCarouselImages) && !empty($validated['existing_images'])) {
            foreach ($validated['existing_images'] as $idx => $path) {
                if (!empty($path)) {
                    $existingImages[] = $path;
                }
            }
        }

        // Delete removed images from storage
        $oldImages = $slide->images ?? [];
        $deletedImages = $validated['deleted_existing_images'] ?? [];
        
        if (!$useImages && !$useCarouselImages) {
            // Non-image type: delete all old images from storage
            foreach ($oldImages as $old) {
                Storage::disk('public')->delete($old);
            }
            $existingImages = [];
        } else {
            // Image type: delete images that were removed
            foreach ($oldImages as $old) {
                if (!in_array($old, $existingImages) && !in_array($old, $deletedImages)) {
                    Storage::disk('public')->delete($old);
                }
            }
        }

        // Add new uploads
        $imagePaths = ($useImages || $useCarouselImages) ? $existingImages : [];
        if (($useImages || $useCarouselImages) && $request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $imagePaths[] = $img->store('features/slideshow', 'public');
            }
        }

        // Process image URLs (filter empty values)
        $imageUrls = [];
        if (($useImages || $useCarouselImages) && !empty($validated['image_urls'])) {
            $imageUrls = array_values(array_filter(array_map('trim', $validated['image_urls']), function($url) {
                return !empty($url);
            }));
        }
        
        // Handle carousel videos (for text_carousel type)
        $carouselVideoPaths = [];
        if ($useCarouselVideo && $request->hasFile('carousel_videos')) {
            foreach ($request->file('carousel_videos') as $video) {
                $carouselVideoPaths[] = $video->store('features/slideshow/videos', 'public');
            }
        }

        // Handle video file upload/delete for carousel videos
        $videoFilePath = $slide->video_file;
        $carouselVideoUrls = [];
        if ($useCarouselVideo) {
            // Get existing carousel videos from database
            $existingCarouselVideos = [];
            $oldVf = $slide->video_file;
            if ($oldVf) {
                if (is_array($oldVf)) {
                    $existingCarouselVideos = $oldVf;
                } elseif (is_string($oldVf) && str_starts_with($oldVf, '[')) {
                    $decoded = json_decode($oldVf, true);
                    $existingCarouselVideos = is_array($decoded) ? $decoded : [];
                }
            }
            
            // Get kept carousel videos from form
            $keptVideos = [];
            $existingInput = $request->input('existing_carousel_videos');
            if ($existingInput) {
                $decodedExisting = is_array($existingInput) ? $existingInput : json_decode($existingInput, true);
                $keptVideos = is_array($decodedExisting) ? $decodedExisting : [];
            }
            
            // Delete videos that are no longer in the kept list
            foreach ($existingCarouselVideos as $oldVideo) {
                if (!in_array($oldVideo, $keptVideos)) {
                    Storage::disk('public')->delete($oldVideo);
                }
            }
            
            // Get unified video order from form
            $unifiedOrder = [];
            $unifiedInput = $request->input('unified_video_order');
            if ($unifiedInput) {
                $decodedOrder = is_array($unifiedInput) ? $unifiedInput : json_decode($unifiedInput, true);
                $unifiedOrder = is_array($decodedOrder) ? $decodedOrder : [];
            }
            
            // Build ordered URLs and uploads based on unified order
            $orderedUrls = [];
            $orderedUploads = [];

            if (!empty($unifiedOrder)) {
                foreach ($unifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $url = trim($item['urlValue'] ?? '');
                        if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                            $orderedUrls[] = $url;
                        }
                    } elseif ($type === 'upload') {
                        $uploadPath = $item['uploadPath'] ?? '';
                        if (!empty($uploadPath)) {
                            $orderedUploads[] = $uploadPath;
                        }
                    } elseif ($type === 'newUpload') {
                        // Use array_shift to take new uploads in the order they appear in unifiedOrder
                        // This ensures the new upload is placed at the correct position in orderedUploads
                        if (!empty($carouselVideoPaths)) {
                            $orderedUploads[] = array_shift($carouselVideoPaths);
                        }
                    }
                }
            } else {
                // Fallback: get URLs from form directly (carousel_video_urls[])
                if (!empty($validated['carousel_video_urls'])) {
                    $orderedUrls = array_values(array_filter(array_map('trim', $validated['carousel_video_urls']), function($url) {
                        return !empty($url);
                    }));
                }
                // Fallback: use all carouselVideoPaths
                $orderedUploads = $carouselVideoPaths;
            }

            // Set carouselVideoUrls from ordered URLs
            $carouselVideoUrls = $orderedUrls;

            // Save original order for caption mapping (before normalization)
            $originalUnifiedOrder = $unifiedOrder;

            // Normalize unifiedOrder: convert all newUpload entries to upload entries with resolved paths
            $normalizedOrder = [];
            $newUploadIdx = 0;
            foreach ($unifiedOrder as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'newUpload') {
                    // Find the path that was stored for this new upload (orderedUploads contains both existing and new in order)
                    // We need to find the new upload path by counting newUploads seen so far
                    $uploadPath = null;
                    $newCount = 0;
                    foreach ($orderedUploads as $path) {
                        // Check if this path is from keptVideos (existing) or new
                        if (!in_array($path, $keptVideos)) {
                            if ($newCount === $newUploadIdx) {
                                $uploadPath = $path;
                                break;
                            }
                            $newCount++;
                        }
                    }
                    $normalizedOrder[] = [
                        'type' => 'upload',
                        'uploadPath' => $uploadPath,
                        'uploadIndex' => count(array_filter($normalizedOrder, fn($i) => $i['type'] === 'upload')),
                    ];
                    $newUploadIdx++;
                } else {
                    $normalizedOrder[] = $item;
                }
            }
            $unifiedOrder = $normalizedOrder;

            // Store uploads in video_file - ordered uploads (existing + new)
            $videoFilePath = !empty($orderedUploads) ? json_encode(array_values($orderedUploads)) : null;
        } elseif (!$useVideo) {
            // Clear video data if slide type doesn't use video
            if ($slide->video_file) {
                // Check if it's a JSON array (carousel videos)
                $existingFiles = $slide->video_file;
                if (is_string($existingFiles) && str_starts_with($existingFiles, '[')) {
                    $decoded = json_decode($existingFiles, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $oldFile) {
                            Storage::disk('public')->delete($oldFile);
                        }
                    }
                } else {
                    Storage::disk('public')->delete($existingFiles);
                }
                $videoFilePath = null;
            }
        } else {
            // Slide type uses video (single video)
            if ($request->input('delete_existing_video') === '1') {
                // Delete existing video file if user chose to delete
                if ($slide->video_file) {
                    Storage::disk('public')->delete($slide->video_file);
                    $videoFilePath = null;
                }
            } elseif ($request->hasFile('video_file')) {
                // Delete old video file if exists, then upload new one
                if ($slide->video_file) {
                    Storage::disk('public')->delete($slide->video_file);
                }
                $videoFilePath = $request->file('video_file')->store('features/slideshow/videos', 'public');
            }
        }

        // Determine primary video URL based on selected method
        $primaryVideoUrl = null;
        if ($useVideo) {
            $useVideoMethod = $request->input('video_method') === 'url';
            $clearExistingUrl = $request->input('clear_existing_url') === '1';
            
            if ($useVideoMethod) {
                // Using URL method - get video URL (single video only)
                if (!empty($validated['video_url'])) {
                    $primaryVideoUrl = trim($validated['video_url']);
                }
                // Clear existing URL if user chose to delete it
                if ($clearExistingUrl) {
                    $primaryVideoUrl = null;
                }
            }
            // If using upload method, primaryVideoUrl stays null and video_file is used
        } else {
            // Non-video type: clear any existing video_url from database
            $primaryVideoUrl = null;
        }

        // Build info_popup - only include info if there's corresponding content
        $infoPopup = [];
        $captionModes = $validated['info_popup_mode_images'] ?? [];
        $captionQaData = $request->input('info_popup_qa_images', []);

        if (($useImages || $useCarouselImages)) {
            // Count existing images (before new uploads)
            $existingImageCount = count($existingImages);
            $uploadedCount = count($imagePaths); // This includes both existing and new
            $urlCount = count($imageUrls);

            // Process info_popup_images - this contains captions for existing images only
            if (!empty($validated['info_popup_images'])) {
                foreach ($validated['info_popup_images'] as $idx => $caption) {
                    $mode = $captionModes[$idx] ?? 'single';
                    $qaItems = $captionQaData[$idx] ?? [];
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value !== null) {
                        $infoPopup[(string)$idx] = $value;
                    }
                }
            }

            // Process info_popup_new_images[] for new uploads
            if (!empty($validated['info_popup_new_images'])) {
                $newImageModes = $request->input('info_popup_mode_new_images', []);
                $newImageQaData = $request->input('info_popup_qa_new_images', []);
                foreach ($validated['info_popup_new_images'] as $idx => $caption) {
                    $actualIndex = $existingImageCount + $idx;
                    $mode = $newImageModes[$idx] ?? ($captionModes[$actualIndex] ?? 'single');
                    $qaItems = $newImageQaData[$idx] ?? ($captionQaData[$actualIndex] ?? []);
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value !== null) {
                        $infoPopup[(string)$actualIndex] = $value;
                    }
                }
            }
        }
        $hasVideo = !empty($primaryVideoUrl) || !empty($videoFilePath);
        if ($useVideo) {
            // Upload video caption (only when upload method is used or existing file)
            if ($hasVideo && empty($primaryVideoUrl)) {
                $videoMode = $validated['info_popup_mode_video'] ?? 'single';
                $videoQaItems = $request->input('info_popup_qa_video', []);
                $videoValue = $this->buildCaptionValue($videoMode, $validated['info_popup_video'] ?? null, $videoQaItems);
                if ($videoValue !== null) {
                    $infoPopup['video'] = $videoValue;
                }
            }
            // URL video caption (always when URL method is used)
            $useVideoUrlMethod = $request->input('video_method') === 'url';
            if ($useVideoUrlMethod && (!empty($primaryVideoUrl) || !empty($validated['video_url']))) {
                $videoUrlMode = $validated['info_popup_mode_video_url'] ?? 'single';
                $videoUrlQaItems = $request->input('info_popup_qa_video_url', []);
                $videoUrlValue = $this->buildCaptionValue($videoUrlMode, $validated['info_popup_video_url'] ?? null, $videoUrlQaItems);
                if ($videoUrlValue !== null) {
                    $infoPopup['video_url'] = $videoUrlValue;
                }
            }
        }
        if ($useCarouselVideo) {
            // Always store the normalized order for proper reconstruction on load
            $infoPopup['carousel_video_order'] = $unifiedOrder;

            if (!empty($validated['info_popup_carousel_videos'])) {
                $infoPopup['carousel_videos'] = [];

                // Build mapping from caption key to storage info using ORIGINAL order (before normalization)
                // because form sends keys like newUpload_X which don't exist in normalized order
                $captionToStorage = [];
                $urlStorageIdx = 0;
                $uploadStorageIdx = 0;

                foreach ($originalUnifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $urlIdx = $item['urlIndex'] ?? 0;
                        $captionToStorage['url_' . $urlIdx] = ['type' => 'url', 'storageIdx' => $urlStorageIdx];
                        $urlStorageIdx++;
                    } elseif ($type === 'upload') {
                        $uploadIdx = $item['uploadIndex'] ?? 0;
                        $captionToStorage['upload_' . $uploadIdx] = ['type' => 'upload', 'storageIdx' => $uploadStorageIdx];
                        $uploadStorageIdx++;
                    } elseif ($type === 'newUpload') {
                        $newUploadIdx = $item['newUploadIndex'] ?? 0;
                        $captionToStorage['newUpload_' . $newUploadIdx] = ['type' => 'upload', 'storageIdx' => $uploadStorageIdx];
                        $uploadStorageIdx++;
                    }
                }

                // Process captions: remap newUpload_X keys to sequential upload_X keys
                $carouselVideoModes = $validated['info_popup_mode_carousel_videos'] ?? [];
                $carouselVideoQaData = $request->input('info_popup_qa_carousel_videos', []);

                foreach ($validated['info_popup_carousel_videos'] as $key => $caption) {
                    $mode = $carouselVideoModes[$key] ?? 'single';
                    $qaItems = $carouselVideoQaData[$key] ?? [];
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value === null) continue;

                    if (str_starts_with($key, 'newUpload_')) {
                        $storageKey = 'upload_' . ($captionToStorage[$key]['storageIdx'] ?? 0);
                        $infoPopup['carousel_videos'][$storageKey] = $value;
                    } else {
                        $infoPopup['carousel_videos'][$key] = $value;
                    }
                }
            }
        }

        // Determine feature_page_id
        $featurePageId = $page ? $page->id : ($validated['feature_page_id'] ?? null);

        // Hero: hanya boleh upload ATAU URL, tidak boleh keduanya
        if (($useImages || $useCarouselImages) && $slideType === 'hero') {
            $hasUploadedImages = !empty($imagePaths);
            $hasUrlImages = !empty($imageUrls);

            if ($hasUploadedImages && $hasUrlImages) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL.'], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image_conflict' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL, tidak boleh keduanya sekaligus.']);
            }
        }

        $slide->update([
            'feature_page_id' => $featurePageId,
            'slide_type'     => $validated['slide_type'],
            'title'          => $validated['title'] ?? null,
            'title_en'       => !empty($validated['title']) ? $translationService->translate($validated['title']) : null,
            'subtitle'       => $validated['subtitle'] ?? null,
            'subtitle_en'    => !empty($validated['subtitle']) ? $translationService->translate($validated['subtitle']) : null,
            'description'    => $validated['description'] ?? null,
            'description_en' => !empty($validated['description']) ? $translationService->translate($validated['description']) : null,
            'images'         => ($useImages || $useCarouselImages) ? ($imagePaths ?: null) : null,
            'image_urls'     => ($useImages || $useCarouselImages) ? ($imageUrls ?: null) : null,
            'carousel_video_urls' => $useCarouselVideo ? ($carouselVideoUrls ?: null) : null,
            'video_url'      => $useVideo ? $primaryVideoUrl : null,
            'video_file'     => ($useVideo || $useCarouselVideo) ? $videoFilePath : null,
            'layout'         => $validated['layout'],
            'bg_color'      => $validated['bg_color'] ?? null,
            'info_popup'    => $infoPopup ?: null,
            'order'          => $validated['order'],
        ]);

        // Redirect based on context
        if ($page) {
            return redirect()->route('cms.features.slideshow.pages.slides.index', [$feature, $page])
                ->with('success', 'Slide berhasil diperbarui.');
        }

        return redirect()->route('cms.features.slideshow.index', $feature)
            ->with('success', 'Slide berhasil diperbarui.');
    }

    /**
     * Destroy slide (legacy)
     */
    public function destroy(Feature $feature, VirtualSlideshowSlide $slide)
    {
        return $this->destroySlideData($feature, $slide);
    }

    /**
     * Destroy slide for specific page
     */
    public function destroySlide(Feature $feature, $pageId, VirtualSlideshowSlide $slide)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        return $this->destroySlideData($feature, $slide, $page);
    }

    /**
     * Shared method to destroy slide
     */
    private function destroySlideData(Feature $feature, VirtualSlideshowSlide $slide, ?VirtualSlideshowPage $page = null)
    {
        // Delete images from storage
        if ($slide->images) {
            foreach ($slide->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }
        
        // Delete video file from storage
        if ($slide->video_file) {
            Storage::disk('public')->delete($slide->video_file);
        }
        
        $slide->delete();

        // Redirect based on context
        if ($page) {
            return redirect()->route('cms.features.slideshow.pages.slides.index', [$feature, $page])
                ->with('success', 'Slide berhasil dihapus.');
        }

        return redirect()->route('cms.features.slideshow.index', $feature)
            ->with('success', 'Slide berhasil dihapus.');
    }
}
