<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Services\Portal\GuestBookingService;
use App\Services\Portal\GuestPageService;
use App\Services\Portal\GuestPackageService;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __construct(
        private GuestPageService $guestPageService,
        private GuestPackageService $guestPackageService,
        private GuestBookingService $guestBookingService
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

    public function portfolio(): View
    {
        return $this->guestPageService->render('portfolio.page');
    }

    public function booking(): View
    {
        return $this->guestPageService->render('booking.form', $this->guestBookingService->getFormPayload());
    }

    public function aboutEtherno(): View
    {
        return $this->guestPageService->render('about.etherno');
    }
}
