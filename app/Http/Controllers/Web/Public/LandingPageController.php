<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Services\Portal\GuestPageService;
use App\Services\Portal\GuestPackageService;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __construct(
        private GuestPageService $guestPageService,
        private GuestPackageService $guestPackageService
    ) {
    }

    public function index(): View
    {
        return $this->guestPageService->render('landing', $this->guestPackageService->getLandingPayload());
    }

    public function packages(): View
    {
        return $this->guestPageService->render('packages.page', $this->guestPackageService->getAllPackagesPayload());
    }

    public function booking(): View
    {
        return $this->guestPageService->render('booking.form');
    }
}
