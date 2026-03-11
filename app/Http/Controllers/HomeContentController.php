<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomeContentController extends Controller
{
    /**
     * Show the Beranda content editor.
     */
    public function edit()
    {
        $contentId = config('app.locale', 'id');
        $idContent = $this->loadLangFile('id');
        $enContent = $this->loadLangFile('en');

        return view('cms.home.edit', compact('idContent', 'enContent'));
    }

    /**
     * Save the Beranda content.
     * Saves Indonesian text, then auto-translates and saves to English.
     */
    public function update(Request $request, TranslationService $translationService)
    {
        $data = $request->except(['_token', '_method', 'locale']);

        // 1. Save Indonesian version
        $this->saveLangFile('id', $data);

        // 2. Load full ID file (merged), translate, and save EN version
        $fullIdContent = $this->loadLangFile('id');
        $translatedData = $translationService->translateArray($fullIdContent);
        $this->saveLangFile('en', $translatedData, replace: true);

        return redirect()->route('cms.home.edit')
            ->with('success', 'Konten Beranda berhasil disimpan');
    }

    /**
     * Load language file as array.
     */
    private function loadLangFile(string $locale): array
    {
        $path = resource_path("lang/{$locale}/home.php");
        if (File::exists($path)) {
            return include $path;
        }

        return [];
    }

    /**
     * Save data back to language file.
     * When replace=true, overwrites the file entirely instead of merging.
     */
    private function saveLangFile(string $locale, array $data, bool $replace = false): void
    {
        $path = resource_path("lang/{$locale}/home.php");

        if ($replace) {
            $updated = $data;
        } else {
            // Load existing content
            $existing = File::exists($path) ? include $path : [];
            // Merge with submitted data (only update known keys)
            $updated = $this->mergeDeep($existing, $data);
        }

        // Write back as PHP array
        $content = "<?php\n\nreturn ".$this->varExport($updated, true).";\n";
        File::put($path, $content);
    }

    /**
     * Deep merge arrays.
     */
    private function mergeDeep(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = $this->mergeDeep($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }

    /**
     * Export variable as formatted PHP code.
     */
    private function varExport($var, bool $indent = false, int $level = 0): string
    {
        if (is_array($var)) {
            $pad = str_repeat('    ', $level + 1);
            $closePad = str_repeat('    ', $level);
            $items = [];
            $isList = array_keys($var) === range(0, count($var) - 1);
            foreach ($var as $k => $v) {
                $key = $isList ? '' : var_export($k, true).' => ';
                $items[] = $pad.$key.$this->varExport($v, $indent, $level + 1);
            }

            return "[\n".implode(",\n", $items).",\n{$closePad}]";
        }

        return var_export($var, true);
    }
}
