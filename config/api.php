<?php

return [
    'auth' => [
        'demo_profile' => [
            'name' => env('API_PROFILE_NAME', 'MCX Demo User'),
            'email' => env('API_PROFILE_EMAIL', 'demo@example.com'),
            'phone' => env('API_PROFILE_PHONE', '+91 9999999999'),
        ],
    ],
    'mobile_app' => [
        'support' => [
            'phone' => env('APP_SUPPORT_PHONE', ''),
            'message' => env('APP_SUPPORT_MESSAGE', 'Support is available during market hours.'),
            'announcement' => env('APP_ANNOUNCEMENT', ''),
        ],
        'kyc' => [
            'pan_example' => env('APP_KYC_PAN_EXAMPLE', 'ABCDE1234F'),
            'rules' => array_values(array_filter(array_map(
                'trim',
                explode('|', (string) env('APP_KYC_RULES', 'PAN must belong to the account holder|Name must match PAN records'))
            ))),
            'limits_warning' => env('APP_LIMITS_WARNING', 'Trading limits may change based on KYC verification status.'),
        ],
    ],
];
