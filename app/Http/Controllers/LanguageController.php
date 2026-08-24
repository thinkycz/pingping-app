<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'in:en,cs'],
        ]);

        $request->session()->put('locale', $validated['language']);

        if ($request->user()) {
            $request->user()->update(['locale' => $validated['language']]);
        }

        return redirect()->back();
    }
}
