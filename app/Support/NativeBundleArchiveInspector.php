<?php

namespace App\Support;

use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

final class NativeBundleArchiveInspector
{
    /**
     * @return list<string>
     */
    public function inspect(string $archivePath): array
    {
        $archive = new ZipArchive;

        if ($archive->open($archivePath) !== true) {
            throw new RuntimeException("Unable to open native bundle archive: {$archivePath}");
        }

        try {
            return [
                ...$this->inspectPaths($archive),
                ...$this->inspectEnvironment($archive),
            ];
        } finally {
            $archive->close();
        }
    }

    /**
     * @return list<string>
     */
    private function inspectPaths(ZipArchive $archive): array
    {
        $forbiddenPaths = $this->stringList(config('nativephp.bundle_verification.forbidden_paths', []));
        $violations = [];

        for ($index = 0; $index < $archive->numFiles; $index++) {
            $entry = ltrim((string) $archive->getNameIndex($index), '/');

            foreach ($forbiddenPaths as $forbiddenPath) {
                $path = trim($forbiddenPath, '/');

                if ($entry === $path || Str::startsWith($entry, $path.'/') || Str::is($path, $entry)) {
                    $violations[] = "Forbidden path: {$entry}";

                    break;
                }
            }
        }

        return array_values(array_unique($violations));
    }

    /**
     * @return list<string>
     */
    private function inspectEnvironment(ZipArchive $archive): array
    {
        $contents = $archive->getFromName('.env');

        if ($contents === false) {
            return ['Missing bundled .env file.'];
        }

        $forbiddenKeys = $this->stringList(config('nativephp.cleanup_env_keys', []));
        $violations = [];

        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || Str::startsWith($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            $key = Str::before($line, '=');

            if (Str::is($forbiddenKeys, $key)) {
                $violations[] = "Forbidden environment key: {$key}";
            }
        }

        return array_values(array_unique($violations));
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, is_string(...)));
    }
}
