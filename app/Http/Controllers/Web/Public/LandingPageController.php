<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Services\Portal\GuestPageService;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __construct(private GuestPageService $guestPageService)
    {
    }

    public function index(): View
    {
        return $this->guestPageService->render('landing');
    }

    public function booking(): View
    {
        return $this->guestPageService->render('booking.form');
    }
}
