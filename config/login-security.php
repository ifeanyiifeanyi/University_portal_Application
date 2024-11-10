<?php

return [
    // How many days to keep login activity records
    'activity_retention_days' => env('LOGIN_ACTIVITY_RETENTION_DAYS', 90),

    // Other login security related configs
    'max_attempts' => env('LOGIN_MAX_ATTEMPTS', 5),
    'decay_minutes' => env('LOGIN_DECAY_MINUTES', 1),
    'notify_on_new_device' => env('LOGIN_NOTIFY_NEW_DEVICE', true),

    'suspicious_activity' => [
        'window_minutes' => env('SUSPICIOUS_ACTIVITY_WINDOW', 60),
        'max_failed_attempts' => env('SUSPICIOUS_ACTIVITY_MAX_ATTEMPTS', 3),
    ],
];
