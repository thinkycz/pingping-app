<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'in:en,cs'],
        ]);

        Session::put('locale', $validated['language']);

        return redirect()->back();
    }
}
