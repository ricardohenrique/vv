<?php

use App\NativeComponents\WebExperience;
use Native\Mobile\Testing\Native;

it('opens the React experience from the mobile entry point', function () {
    Native::visit('/mobile')
        ->assertScreen(WebExperience::class)
        ->assertNavTitle('React + Inertia')
        ->assertElement('native_root_stack', fn (array $node) => ($node['props']['back'] ?? false) === false)
        ->assertElement('webview', fn (array $node) => ($node['props']['src'] ?? null) === '/web-experience-entry'
            && ($node['props']['php'] ?? null) === true);
});
