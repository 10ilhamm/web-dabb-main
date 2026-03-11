<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\VirtualBookPage;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VirtualBookPageController extends Controller
{
    /**
     * List pages for a feature (CMS).
     */
    public function index(Feature $feature)
    {
        $feature->load(['virtualBookPages', 'parent']);

        return view('cms.features.virtual_book_pages.index', compact('feature'));
    }

    /**
     * Show create form for a new page.
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $maxOrder = $feature->virtualBookPages()->max('order') ?? 0;

        return view('cms.features.virtual_book_pages.create', compact('feature', 'maxOrder'));
    }

    /**
     * Show edit form for a page.
     */
    public function edit(Feature $feature, VirtualBookPage $virtualBookPage)
    {
        $feature->load('parent');

        return view('cms.features.virtual_book_pages.edit', compact('feature', 'virtualBookPage'));
    }

    /**
     * Store a new page for a virtual book.
     */
    public function store(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_height' => 'nullable|integer|min:10|max:100',
            'is_cover' => 'boolean',
            'is_back_cover' => 'boolean',
            'order' => 'required|integer|min:0',
        ]);

        $validated['feature_id'] = $feature->id;
        $validated['image_height'] = $validated['image_height'] ?? 50;

        // Handle translation
        $validated['title_en'] = !empty($validated['title'])
            ? $translationService->translate($validated['title'])
            : null;
        $validated['content_en'] = !empty($validated['content'])
            ? $translationService->translate($validated['content'])
            : null;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('features/virtual-books', 'public');
        }

        VirtualBookPage::create($validated);

        return redirect()->route('cms.features.virtual_book_pages.index', $feature)
            ->with('success', 'Halaman buku berhasil ditambahkan');
    }

    /**
     * Show page detail - manage single page (CMS).
     */
    public function show(Feature $feature, VirtualBookPage $virtualBookPage)
    {
        $feature->load('parent');

        return view('cms.features.virtual_book_pages.show', compact('feature', 'virtualBookPage'));
    }

    /**
     * Update a page.
     */
    public function update(Request $request, Feature $feature, VirtualBookPage $virtualBookPage, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_height' => 'nullable|integer|min:10|max:100',
            'remove_image' => 'boolean',
            'is_cover' => 'boolean',
            'is_back_cover' => 'boolean',
            'order' => 'required|integer|min:0',
        ]);

        // Set default image height if not provided
        if (!isset($validated['image_height'])) {
            $validated['image_height'] = $virtualBookPage->image_height ?? 50;
        }

        // Handle translation
        $validated['title_en'] = !empty($validated['title'])
            ? $translationService->translate($validated['title'])
            : null;
        $validated['content_en'] = !empty($validated['content'])
            ? $translationService->translate($validated['content'])
            : null;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($virtualBookPage->image) {
                Storage::disk('public')->delete($virtualBookPage->image);
            }
            $validated['image'] = $request->file('image')->store('features/virtual-books', 'public');
        } elseif ($request->boolean('remove_image')) {
            // Remove existing image
            if ($virtualBookPage->image) {
                Storage::disk('public')->delete($virtualBookPage->image);
            }
            $validated['image'] = null;
        } else {
            // Keep existing image - remove from validated to not update
            unset($validated['image']);
        }

        $virtualBookPage->update($validated);

        return redirect()->route('cms.features.virtual_book_pages.index', $feature)
            ->with('success', 'Halaman buku berhasil diperbarui');
    }

    /**
     * Delete a page.
     */
    public function destroy(Feature $feature, VirtualBookPage $virtualBookPage)
    {
        // Delete image
        if ($virtualBookPage->image) {
            Storage::disk('public')->delete($virtualBookPage->image);
        }

        $virtualBookPage->delete();

        return redirect()->route('cms.features.virtual_book_pages.index', $feature)
            ->with('success', 'Halaman buku berhasil dihapus');
    }

    /**
     * Update virtual book settings (cover, thumbnail).
     */
    public function updateSettings(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'book_cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'book_thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_book_cover' => 'boolean',
            'remove_book_thumbnail' => 'boolean',
        ]);

        // Handle book cover upload
        if ($request->hasFile('book_cover')) {
            if ($feature->book_cover) {
                Storage::disk('public')->delete($feature->book_cover);
            }
            $feature->book_cover = $request->file('book_cover')->store('features/virtual-books', 'public');
        } elseif ($request->boolean('remove_book_cover')) {
            if ($feature->book_cover) {
                Storage::disk('public')->delete($feature->book_cover);
            }
            $feature->book_cover = null;
        }

        // Handle book thumbnail upload
        if ($request->hasFile('book_thumbnail')) {
            if ($feature->book_thumbnail) {
                Storage::disk('public')->delete($feature->book_thumbnail);
            }
            $feature->book_thumbnail = $request->file('book_thumbnail')->store('features/virtual-books', 'public');
        } elseif ($request->boolean('remove_book_thumbnail')) {
            if ($feature->book_thumbnail) {
                Storage::disk('public')->delete($feature->book_thumbnail);
            }
            $feature->book_thumbnail = null;
        }

        $feature->save();

        return redirect()->route('cms.features.virtual_book_pages.index', $feature)
            ->with('success', 'Pengaturan buku berhasil diperbarui');
    }
}
