<?php

namespace App\Console\Commands;

use App\Support\NativeBundleArchiveInspector;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

class VerifyNativeBundle extends Command
{
    /** @var string */
    protected $signature = 'native:verify-bundle {archive : Path to app.zip or laravel_bundle.zip}';

    /** @var string */
    protected $description = 'Fail when a NativePHP Laravel bundle contains forbidden files or environment keys';

    public function handle(NativeBundleArchiveInspector $inspector): int
    {
        $archive = (string) $this->argument('archive');
        $archivePath = $this->absolutePath($archive);

        if (! is_file($archivePath)) {
            $this->components->error("Native bundle archive does not exist: {$archivePath}");

            return self::FAILURE;
        }

        try {
            $violations = $inspector->inspect($archivePath);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($violations !== []) {
            $this->components->error('Native bundle verification failed.');

            foreach ($violations as $violation) {
                $this->line(" - {$violation}");
            }

            return self::FAILURE;
        }

        $this->components->info('Native bundle verification passed.');

        return self::SUCCESS;
    }

    private function absolutePath(string $path): string
    {
        if (Str::startsWith($path, ['/']) || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1) {
            return $path;
        }

        return base_path($path);
    }
}
