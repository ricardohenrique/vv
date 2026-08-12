<?php

use ZipArchive;

function createNativeBundleArchive(array $files): string
{
    $path = tempnam(sys_get_temp_dir(), 'native-bundle-');

    if ($path === false) {
        throw new RuntimeException('Unable to create temporary archive path.');
    }

    $archive = new ZipArchive;
    $archive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    foreach ($files as $name => $contents) {
        $archive->addFromString($name, $contents);
    }

    $archive->close();

    return $path;
}

it('accepts a clean native bundle archive', function () {
    $archive = createNativeBundleArchive([
        '.env' => "APP_NAME=Boilerplate\nNATIVEPHP_API_URL=https://api.example.com",
        'app/Models/User.php' => '<?php',
    ]);

    try {
        $this->artisan('native:verify-bundle', ['archive' => $archive])
            ->expectsOutputToContain('Native bundle verification passed.')
            ->assertSuccessful();
    } finally {
        unlink($archive);
    }
});

it('rejects forbidden paths and environment keys in a native bundle', function () {
    $archive = createNativeBundleArchive([
        '.env' => "APP_KEY=base64:not-for-release\nDB_PASSWORD=secret",
        '.ai/skills/example/SKILL.md' => 'development instructions',
        'app/Models/User.php' => '<?php',
    ]);

    try {
        $this->artisan('native:verify-bundle', ['archive' => $archive])
            ->expectsOutputToContain('Native bundle verification failed.')
            ->expectsOutputToContain('Forbidden path: .ai/skills/example/SKILL.md')
            ->expectsOutputToContain('Forbidden environment key: APP_KEY')
            ->expectsOutputToContain('Forbidden environment key: DB_PASSWORD')
            ->assertFailed();
    } finally {
        unlink($archive);
    }
});
