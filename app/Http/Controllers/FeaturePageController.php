<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\FeaturePage;
use App\Models\FeaturePageSection;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FeaturePageController extends Controller
{
    /**
     * List pages for a feature (CMS).
     */
    public function index(Feature $feature)
    {
        $feature->load(['pages' => function ($q) {
            $q->withCount('sections');
        }, 'parent']);

        return view('cms.features.pages.index', compact('feature'));
    }

    /**
     * Store a new page for a feature.
     */
    public function store(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        $validated['feature_id'] = $feature->id;
        $validated['title_en'] = $translationService->translate($validated['title']);
        if (! empty($validated['description'])) {
            $validated['description_en'] = $translationService->translate($validated['description']);
        }

        FeaturePage::create($validated);

        return redirect()->route('cms.features.pages.index', $feature)
            ->with('success', __('cms.feature_pages.flash.page_added'));
    }

    /**
     * Show page detail - manage sections (CMS).
     */
    public function show(Feature $feature, FeaturePage $page)
    {
        $page->load('sections');
        $feature->load('parent');

        return view('cms.features.pages.show', compact('feature', 'page'));
    }

    /**
     * Update a page.
     */
    public function update(Request $request, Feature $feature, FeaturePage $page, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        $validated['title_en'] = $translationService->translate($validated['title']);
        $validated['description_en'] = ! empty($validated['description'])
            ? $translationService->translate($validated['description'])
            : null;

        $page->update($validated);

        return redirect()->route('cms.features.pages.index', $feature)
            ->with('success', __('cms.feature_pages.flash.page_updated'));
    }

    /**
     * Delete a page.
     */
    public function destroy(Feature $feature, FeaturePage $page)
    {
        // Delete section images
        foreach ($page->sections as $section) {
            $this->deleteSectionImages($section);
        }

        $page->delete();

        return redirect()->route('cms.features.pages.index', $feature)
            ->with('success', __('cms.feature_pages.flash.page_deleted'));
    }

    /**
     * Store a new section for a page.
     */
    public function storeSection(Request $request, Feature $feature, FeaturePage $page, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array', // unlimited
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_positions' => 'nullable|array',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/sections', 'public');
            }
        }

        FeaturePageSection::create([
            'feature_page_id' => $page->id,
            'title' => $validated['title'],
            'title_en' => $translationService->translate($validated['title']),
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description'])
                ? $translationService->translate($validated['description'])
                : null,
            'images' => $imagePaths ?: null,
            'image_positions' => $validated['image_positions'] ?? null,
            'order' => $validated['order'],
        ]);

        return redirect()->route('cms.features.pages.show', [$feature, $page])
            ->with('success', __('cms.feature_pages.flash.section_added'));
    }

    /**
     * Update a section.
     */
    public function updateSection(Request $request, Feature $feature, FeaturePage $page, FeaturePageSection $section, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array', // unlimited
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'image_positions' => 'nullable|array',
        ]);

        // Keep existing images that weren't removed
        $existingImages = $validated['existing_images'] ?? [];

        // Delete removed images from storage
        $oldImages = $section->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        // Add new uploaded images
        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/sections', 'public');
            }
        }

        $section->update([
            'title' => $validated['title'],
            'title_en' => $translationService->translate($validated['title']),
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description'])
                ? $translationService->translate($validated['description'])
                : null,
            'images' => $imagePaths ?: null,
            'image_positions' => $validated['image_positions'] ?? null,
            'order' => $validated['order'],
        ]);

        return redirect()->route('cms.features.pages.show', [$feature, $page])
            ->with('success', __('cms.feature_pages.flash.section_updated'));
    }

    /**
     * Delete a section.
     */
    public function destroySection(Feature $feature, FeaturePage $page, FeaturePageSection $section)
    {
        $this->deleteSectionImages($section);
        $section->delete();

        return redirect()->route('cms.features.pages.show', [$feature, $page])
            ->with('success', __('cms.feature_pages.flash.section_deleted'));
    }

    /**
     * Public: show feature page with sections (paginated).
     */
    public function publicShow(Feature $feature, ?int $pageNum = null, bool $requiresLoginModal = false, ?string $loginModalPreview = null, ?string $loginModalRoomName = null)
    {
        $feature->load('parent');
        $pages = $feature->pages()->withCount('sections')->orderBy('order')->get();

        if ($pages->isEmpty()) {
            abort(404);
        }

        $pageNum = $pageNum ?? 1;
        $currentPage = $pages->values()->get($pageNum - 1);

        if (! $currentPage) {
            abort(404);
        }

        $currentPage->load('sections');

        $virtual3dRooms = $feature->virtual3dRooms()->with('media')->get();

        return view('pages.virtual_3d_tour', [
            'feature'             => $feature,
            'pages'               => $pages,
            'currentPage'         => $currentPage,
            'currentPageNum'      => $pageNum,
            'totalPages'          => $pages->count(),
            'requiresLoginModal'  => $requiresLoginModal,
            'loginModalPreview'   => $loginModalPreview,
            'loginModalRoomName'  => $loginModalRoomName,
            'virtual3dRooms'      => $virtual3dRooms,
        ]);
    }

    /**
     * @internal wrapped call from publicShowByPath
     */
    private function publicShowWithModal(Feature $feature, int $pageNum, bool $requiresLoginModal)
    {
        return $this->publicShow($feature, $pageNum, $requiresLoginModal);
    }

    /**
     * Public: show feature page by path (e.g., /pameran/tetap).
     */
    public function publicShowByPath(Request $request)
    {
        $path = '/'.$request->path;
        $feature = Feature::where('path', $path)->firstOrFail();
        $feature->loadCount('pages');

        // Pages under /pameran/virtual or /pameran-arsip-virtual require authentication — show login modal if guest
        $requiresLoginModal = !Auth::check() && (
            str_contains($path, '/pameran/virtual') || 
            str_contains($path, '/pameran-virtual') ||
            str_contains($path, '/pameran-arsip-virtual')
        );

        // Resolve preview image for the login modal right panel
        $loginModalPreview = null;
        $loginModalRoomName = null;
        if ($requiresLoginModal) {
            // Try direct virtual rooms on this feature
            $firstRoom = $feature->virtual3dRooms()->first() ?? $feature->virtualRooms()->first();

            // If none, check sub-features' virtual rooms
            if (!$firstRoom && method_exists($feature, 'subfeatures')) {
                foreach ($feature->subfeatures as $sub) {
                    $firstRoom = $sub->virtual3dRooms()->first() ?? $sub->virtualRooms()->first();
                    if ($firstRoom) break;
                }
            }
            if ($firstRoom) {
                $imgPath = $firstRoom->thumbnail_path ?? $firstRoom->image_360_path ?? null;
                $loginModalPreview = $imgPath ? asset('storage/'.$imgPath) : null;
                $loginModalRoomName = $firstRoom->name;
            }
        }

        // Virtual 3D Rooms feature — show interactive 4-walls 3D room
        if (method_exists($feature, 'virtual3dRooms')) {
            $virtual3dRooms = $feature->virtual3dRooms()->with('media')->get();

            // Check subfeatures if the parent feature has no virtual 3d rooms
            if ($virtual3dRooms->isEmpty() && method_exists($feature, 'subfeatures')) {
                foreach ($feature->subfeatures as $sub) {
                    if (method_exists($sub, 'virtual3dRooms')) {
                        $virtual3dRooms = $virtual3dRooms->merge($sub->virtual3dRooms()->with('media')->get());
                    }
                }
            }

            if ($virtual3dRooms->isNotEmpty()) {
                // Add first room thumbnail for modal if needed
                if ($requiresLoginModal) {
                    $first3dRoom = $virtual3dRooms->first();
                    $loginModalPreview = $first3dRoom->thumbnail_path ? asset('storage/'.$first3dRoom->thumbnail_path) : null;
                    $loginModalRoomName = $first3dRoom->name;
                }

                return view('pages.virtual_3d_tour', compact(
                    'feature', 'virtual3dRooms', 'requiresLoginModal',
                    'loginModalPreview', 'loginModalRoomName'
                ));
            }
        }

        // Virtual rooms feature (360) — show dedicated 360° tour page
        if (method_exists($feature, 'virtualRooms')) {
            $virtualRooms = $feature->virtualRooms()->withCount('hotspots')->with('hotspots')->get();
            if ($virtualRooms->isNotEmpty()) {
                if ($requiresLoginModal) {
                    $firstRoom = $virtualRooms->first();
                    $imgPath = $firstRoom->thumbnail_path ?? $firstRoom->image_360_path ?? null;
                    $loginModalPreview = $imgPath ? asset('storage/'.$imgPath) : null;
                    $loginModalRoomName = $firstRoom->name;
                }

                return view('pages.virtual_tour', compact(
                    'feature', 'virtualRooms', 'requiresLoginModal',
                    'loginModalPreview', 'loginModalRoomName'
                ));
            }
        }

        // Virtual Book Pages - show flip book
        if ($feature->is_virtual_book || $feature->virtualBookPages()->exists()) {
            $bookPages = $feature->virtualBookPages()->orderBy('order')->get();

            return view('pages.virtual_book', compact(
                'feature', 'bookPages', 'requiresLoginModal',
                'loginModalPreview', 'loginModalRoomName'
            ));
        }

        if ($feature->pages_count > 0) {
            return $this->publicShow($feature, 1, $requiresLoginModal, $loginModalPreview, $loginModalRoomName);
        }

        $virtual3dRooms = $feature->virtual3dRooms()->with('media')->get();
        if ($virtual3dRooms->isEmpty() && method_exists($feature, 'subfeatures')) {
            foreach ($feature->subfeatures as $sub) {
                if (method_exists($sub, 'virtual3dRooms')) {
                    $virtual3dRooms = $virtual3dRooms->merge($sub->virtual3dRooms()->with('media')->get());
                }
            }
        }

        return view('pages.virtual_3d_tour', compact(
            'feature', 'requiresLoginModal', 'loginModalPreview', 'loginModalRoomName', 'virtual3dRooms'
        ));
    }

    private function deleteSectionImages(FeaturePageSection $section): void
    {
        if ($section->images) {
            foreach ($section->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
    }
}
