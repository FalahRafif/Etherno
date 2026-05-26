<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class BlankController extends Controller
{
    /**
     * Redirect legacy blank route to admin dashboard preview.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }
}
