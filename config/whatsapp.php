<?php

return [
    'dummy_data' => [
        'member' => [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'sponsor_name' => 'Jane Smith',
            'upline_name' => 'Bob Johnson',
            'join_date' => date('d M Y'),
            'login_url' => env('APP_URL', 'http://localhost') . '/login',
        ],
        'commission' => [
            'name' => 'John Doe',
            'amount' => 'Rp 500.000',
            'commission_type' => 'Sponsor Bonus',
            'from_member' => 'New Member',
            'date' => date('d M Y'),
            'balance' => 'Rp 2.500.000',
        ],
        'withdrawal' => [
            'name' => 'John Doe',
            'amount' => 'Rp 1.000.000',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'John Doe',
            'status' => 'Diproses',
            'request_date' => date('d M Y'),
        ],
        'admin' => [
            'admin_name' => 'Admin',
            'message' => 'Important notification',
            'date' => date('d M Y'),
            'time' => date('H:i'),
        ],
        'general' => [
            'name' => 'John Doe',
            'message' => 'General message content',
            'date' => date('d M Y'),
            'app_name' => env('APP_NAME', 'Sedekah AI'),
        ],
    ],
];
