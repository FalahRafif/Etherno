<?php

return [
    'roles' => [
        'internal' => ['Admin', 'Petugas'],
        'admin_only' => ['Admin'],
    ],

    'route_prefix_by_role' => [
        'Admin' => 'admin',
        'Petugas' => 'petugas',
    ],

    'dashboard_route_by_role' => [
        'Admin' => 'admin.dashboard',
        'Petugas' => 'petugas.dashboard',
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
            'section' => 'Master Data',
            'items' => [
                [
                    'label' => 'Packages',
                    'route_name' => 'packages',
                    'active' => ['packages'],
                    'roles' => ['Admin'],
                ],
                [
                    'label' => 'Location Rules',
                    'route_name' => 'location.rules',
                    'active' => ['location.rules'],
                    'roles' => ['Admin'],
                ],
                [
                    'label' => 'Customers',
                    'route_name' => 'customers',
                    'active' => ['customers'],
                    'roles' => ['Admin', 'Petugas'],
                ],
                [
                    'label' => 'Settings',
                    'route_name' => 'settings',
                    'active' => ['settings'],
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
