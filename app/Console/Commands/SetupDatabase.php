<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupDatabase extends Command
{
    /** @var string */
    protected $signature = 'app:setup-database';

    /** @var string */
    protected $description = 'Migrate the boilerplate default database after verifying it is the local SQLite file';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->components->error('Automatic setup migrations are limited to the local environment. Run migrations manually after confirming the target.');

            return self::FAILURE;
        }

        if (config('database.default') !== 'sqlite') {
            $this->components->error('Automatic setup migrations are limited to the default SQLite connection. Run migrations manually after confirming the target.');

            return self::FAILURE;
        }

        $configuredDatabase = config('database.connections.sqlite.database');
        $expectedDatabase = realpath(database_path('database.sqlite'));

        if (! is_string($configuredDatabase)
            || realpath($configuredDatabase) === false
            || realpath($configuredDatabase) !== $expectedDatabase) {
            $this->components->error('Automatic setup migrations require database/database.sqlite. Run migrations manually after confirming the configured target.');

            return self::FAILURE;
        }

        return $this->call('migrate', ['--no-interaction' => true]);
    }
}
