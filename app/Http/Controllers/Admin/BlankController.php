<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlankController extends Controller
{
    /**
     * Display the blank admin page.
     */
    public function index()
    {
        return view('pages.admin.blank');
    }
}
