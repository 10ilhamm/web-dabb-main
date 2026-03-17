<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeaturePage;
use App\Models\VirtualSlideshowPage;
use App\Models\VirtualSlideshowSlide;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VirtualSlideshowController extends Controller
{
    /**
     * Show pages table for this slideshow feature
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->slideshowPages()->withCount('slideshowSlides')->orderBy('order')->get();
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
        return view('cms.features.virtual_slideshow.pages.create', compact('feature', 'page'));
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
     * Shared method to store slide data
     */
    private function storeSlideData(Request $request, Feature $feature, ?VirtualSlideshowPage $page, TranslationService $translationService)
    {
        $validated = $request->validate([
            'feature_page_id' => 'nullable|exists:feature_pages,id',
            'slide_type'  => 'required|in:hero,text,carousel,video,text_carousel',
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'video_url'   => 'nullable|string|max:500',
            'layout'      => 'required|in:left,right,center',
            'bg_color'   => 'nullable|string|max:20',
            'order'       => 'required|integer|min:0',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'info_popup_images'   => 'nullable|array',
            'info_popup_images.*' => 'nullable|string',
            'info_popup_video'    => 'nullable|string',
        ]);

        // Upload images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $imagePaths[] = $img->store('features/slideshow', 'public');
            }
        }

        // Build info_popup array
        $infoPopup = [];
        if (!empty($validated['info_popup_images'])) {
            foreach ($validated['info_popup_images'] as $idx => $caption) {
                if (!empty($caption)) {
                    $infoPopup[(string)$idx] = $caption;
                }
            }
        }
        if (!empty($validated['info_popup_video'])) {
            $infoPopup['video'] = $validated['info_popup_video'];
        }

        // Determine feature_page_id
        $featurePageId = $page ? $page->id : ($validated['feature_page_id'] ?? null);

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
            'images'         => $imagePaths ?: null,
            'video_url'      => $validated['video_url'] ?? null,
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
        return view('cms.features.virtual_slideshow.pages.edit', compact('feature', 'page', 'slide'));
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
            'video_url'   => 'nullable|string|max:500',
            'layout'      => 'required|in:left,right,center',
            'bg_color'   => 'nullable|string|max:20',
            'order'       => 'required|integer|min:0',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'existing_images'     => 'nullable|array',
            'existing_images.*'   => 'string',
            'info_popup_images'   => 'nullable|array',
            'info_popup_images.*' => 'nullable|string',
            'info_popup_video'    => 'nullable|string',
        ]);

        // Keep existing images that weren't removed
        $existingImages = $validated['existing_images'] ?? [];

        // Delete removed images
        $oldImages = $slide->images ?? [];
        foreach ($oldImages as $old) {
            if (!in_array($old, $existingImages)) {
                Storage::disk('public')->delete($old);
            }
        }

        // Add new uploads
        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $imagePaths[] = $img->store('features/slideshow', 'public');
            }
        }

        // Build info_popup
        $infoPopup = [];
        if (!empty($validated['info_popup_images'])) {
            foreach ($validated['info_popup_images'] as $idx => $caption) {
                if (!empty($caption)) {
                    $infoPopup[(string)$idx] = $caption;
                }
            }
        }
        if (!empty($validated['info_popup_video'])) {
            $infoPopup['video'] = $validated['info_popup_video'];
        }

        // Determine feature_page_id
        $featurePageId = $page ? $page->id : ($validated['feature_page_id'] ?? null);

        $slide->update([
            'feature_page_id' => $featurePageId,
            'slide_type'     => $validated['slide_type'],
            'title'          => $validated['title'] ?? null,
            'title_en'       => !empty($validated['title']) ? $translationService->translate($validated['title']) : null,
            'subtitle'       => $validated['subtitle'] ?? null,
            'subtitle_en'    => !empty($validated['subtitle']) ? $translationService->translate($validated['subtitle']) : null,
            'description'    => $validated['description'] ?? null,
            'description_en' => !empty($validated['description']) ? $translationService->translate($validated['description']) : null,
            'images'         => $imagePaths ?: null,
            'video_url'      => $validated['video_url'] ?? null,
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
