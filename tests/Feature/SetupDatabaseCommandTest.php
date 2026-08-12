<?php

it('refuses automatic setup migrations outside the local environment', function () {
    $this->artisan('app:setup-database')
        ->expectsOutputToContain('limited to the local environment')
        ->assertExitCode(1);
});

it('refuses automatic setup migrations for a non-SQLite connection', function () {
    app()->instance('env', 'local');
    config()->set('database.default', 'mysql');

    $this->artisan('app:setup-database')
        ->expectsOutputToContain('limited to the default SQLite connection')
        ->assertExitCode(1);
});

it('refuses automatic setup migrations for a different SQLite database', function () {
    app()->instance('env', 'local');
    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', ':memory:');

    $this->artisan('app:setup-database')
        ->expectsOutputToContain('require database/database.sqlite')
        ->assertExitCode(1);
});
