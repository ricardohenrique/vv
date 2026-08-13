<?php

namespace App\Console\Commands;

use Database\Seeders\FreshReviewDatabaseSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('db:fresh-seed')]
#[Description('Rebuild the local database with 500 demo review articles')]
class FreshSeedDatabase extends Command
{
    public function handle(): int
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->components->error('The fresh review database command is limited to local and testing environments.');

            return self::FAILURE;
        }

        $this->components->warn('Rebuilding the database and deleting all existing data.');

        $exitCode = $this->call('migrate:fresh', [
            '--seed' => true,
            '--seeder' => FreshReviewDatabaseSeeder::class,
            '--no-interaction' => true,
        ]);

        if ($exitCode !== self::SUCCESS) {
            return $exitCode;
        }

        $this->components->info('Database rebuilt with 500 articles, 10 categories, 5 authors, and 3–10 tags per article.');

        return self::SUCCESS;
    }
}
