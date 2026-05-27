<?php

use App\Enums\RoleName;

return [
    'roles' => [
        'internal' => [RoleName::Admin->value, RoleName::Petugas->value],
        'admin_only' => [RoleName::Admin->value],
    ],

    'route_prefix_by_role' => [
        RoleName::Admin->value => 'admin',
        RoleName::Petugas->value => 'petugas',
    ],

    'dashboard_route_by_role' => [
        RoleName::Admin->value => 'admin.dashboard',
        RoleName::Petugas->value => 'petugas.dashboard',
    ],

    'panel_title_by_prefix' => [
        'admin' => 'Admin',
        'petugas' => 'Petugas',
    ],

    'guest' => [
        'menu' => [
            [
                'section' => 'Main',
                'items' => [
                    [
                        'label' => 'Portofolio',
                        'route' => 'home',
                        'fragment' => 'portfolio',
                    ],
                    [
                        'label' => 'Paket',
                        'route' => 'home',
                        'fragment' => 'packages',
                    ],
                    [
                        'label' => 'FAQ',
                        'route' => 'home',
                        'fragment' => 'faq',
                    ],
                ],
            ],
        ],
        'cta' => [
            'label' => 'Pesan Sekarang',
            'route' => 'booking.page',
            'aria_label' => 'Pesan Sekarang',
        ],
    ],

    'menu' => [
        [
            'section' => 'Overview',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route_name' => 'dashboard',
                    'active' => ['dashboard'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Calendar & Slots',
                    'route_name' => 'calendar',
                    'active' => ['calendar'],
                    'roles' => ['Admin', 'Petugas'],
                ],
            ],
        ],
        [
            'section' => 'Booking Flow',
            'items' => [
                [
                    'label' => 'Booking Requests',
                    'route_name' => 'bookings.requests',
                    'active' => ['bookings.requests'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Bookings Active',
                    'route_name' => 'bookings.active',
                    'active' => ['bookings.active', 'bookings.detail'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Reschedule Requests',
                    'route_name' => 'reschedules',
                    'active' => ['reschedules'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Cancellations',
                    'route_name' => 'cancellations',
                    'active' => ['cancellations'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Force Majeure',
                    'route_name' => 'force.majeure',
                    'active' => ['force.majeure'],
                    'roles' => ['Admin', 'Petugas'],
                ],
            ],
        ],
        [
            'section' => 'Payments',
            'items' => [
                [
                    'label' => 'DP Verification',
                    'route_name' => 'payments.dp',
                    'active' => ['payments.dp'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Final Payment',
                    'route_name' => 'payments.final',
                    'active' => ['payments.final'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Pricing Review',
                    'route_name' => 'pricing.reviews',
                    'active' => ['pricing.reviews'],
                    'roles' => ['Admin', 'Petugas'],
                ],
            ],
        ],
        [
            'section' => 'Management Data',
            'items' => [
                [
                    'label' => 'Aturan Harga Lokasi',
                    'route_name' => 'location.rules',
                    'active' => ['location.rules', 'location.rules.create', 'location.rules.edit'],
                    'roles' => ['Admin'],
                ],
                [
                    'label' => 'Management User/Akun',
                    'route_name' => 'users',
                    'active' => ['users', 'users.create', 'users.edit'],
                    'roles' => ['Admin'],
                ],
                [
                    'label' => 'Blank (Dev)',
                    'route_name' => 'blank',
                    'active' => ['blank'],
                    'roles' => ['Admin'],
                ],
            ],
        ],
    ],
];
