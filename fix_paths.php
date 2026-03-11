<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$feature = \App\Models\Feature::where('name', 'Pameran Virtual Real')->first();
if ($feature) {
    $feature->path = '/pameran/virtual/real';
    $feature->save();
    echo 'Updated: ' . $feature->path . PHP_EOL;
} else {
    // Try by ID 22
    $feature = \App\Models\Feature::find(22);
    if ($feature) {
        $feature->path = '/pameran/virtual/real';
        $feature->save();
        echo 'Updated by ID: ' . $feature->path . PHP_EOL;
    } else {
        echo 'Feature not found!' . PHP_EOL;
    }
}

// Also fix other sub-features that may be missing leading slash
$fixes = [
    'Pameran Virtual' => '/pameran/virtual',
    'Pameran Virtual Buku' => '/pameran/virtual/buku',
];
foreach ($fixes as $name => $path) {
    $f = \App\Models\Feature::where('name', $name)->first();
    if ($f && !str_starts_with($f->path ?? '', '/')) {
        $f->path = $path;
        $f->save();
        echo "Fixed: {$name} => {$f->path}" . PHP_EOL;
    }
}
echo 'Done.' . PHP_EOL;
