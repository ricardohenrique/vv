<?php

use Illuminate\Support\Facades\File;

it('keeps explicit database queries and persistence out of controllers', function () {
    $forbiddenPatterns = [
        '/\\bDB::/',
        '/::query\s*\(/',
        '/::(?:find|findOrFail|first|firstOrFail|where|create|updateOrCreate)\s*\(/',
        '/->(?:load|loadMissing|refresh|save|update|delete|restore|forceDelete)\s*\(/',
    ];

    foreach (File::allFiles(app_path('Http/Controllers')) as $controller) {
        $source = $controller->getContents();

        foreach ($forbiddenPatterns as $pattern) {
            expect($source)
                ->not->toMatch($pattern, "{$controller->getRelativePathname()} contains an explicit database operation.");
        }
    }
});
