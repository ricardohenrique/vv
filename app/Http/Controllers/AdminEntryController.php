<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminEntryController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return Inertia::render('auth/login');
        }

        abort_unless($user->is_admin, 403);

        return to_route('admin.articles.index');
    }
}
