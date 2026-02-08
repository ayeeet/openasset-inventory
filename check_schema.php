<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$log = [];
$log[] = "Checking 'assets' table schema...";

try {
    $columns = Schema::getColumnListing('assets');
    $log[] = "Columns: " . implode(', ', $columns);

    $hasCategory = in_array('category', $columns);
    $hasCategoryId = in_array('category_id', $columns);

    $log[] = "Has 'category' column: " . ($hasCategory ? 'YES' : 'NO');
    $log[] = "Has 'category_id' column: " . ($hasCategoryId ? 'YES' : 'NO');

    $log[] = "Migration Status:";
    $migrations = DB::table('migrations')->get();
    $found = false;
    foreach ($migrations as $m) {
        if (strpos($m->migration, 'modify_assets_category_column') !== false) {
            $log[] = "Found migration record: " . $m->migration . " (Batch: " . $m->batch . ")";
            $found = true;
        }
    }
    if (!$found) {
        $log[] = "Migration record NOT found.";
    }

} catch (\Exception $e) {
    $log[] = "Error: " . $e->getMessage();
}

file_put_contents(__DIR__ . '/schema_log.txt', implode("\n", $log));
