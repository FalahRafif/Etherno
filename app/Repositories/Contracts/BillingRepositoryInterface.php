<?php

namespace App\Repositories\Contracts;

interface BillingRepositoryInterface extends BaseRepositoryInterface
{
    public function findByBooking(int $bookingId): ?\App\Models\Billing;
}
