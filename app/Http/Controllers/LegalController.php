<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    /** Terms of use (CGU). */
    public function terms(): View
    {
        return view('legal.terms');
    }

    /** Privacy policy / RGPD. */
    public function privacy(): View
    {
        return view('legal.privacy');
    }

    /** Legal notice (mentions légales). */
    public function mentions(): View
    {
        return view('legal.mentions');
    }
}
