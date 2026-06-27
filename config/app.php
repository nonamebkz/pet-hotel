<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Petshop'),
    'url' => rtrim(env('APP_URL', 'http://localhost:8080'), '/'),
    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN),
    'session_lifetime' => (int) env('SESSION_LIFETIME', 120),
    'password_reset_expiry' => (int) env('PASSWORD_RESET_EXPIRY', 60),
    'login_max_attempts' => 5,
    'login_lockout_minutes' => 5,

    // Lokasi petshop (hardcode — untuk hitung jarak antar-jemput)
    'petshop_lat' => (float) env('PETSHOP_LAT', -6.2088),
    'petshop_lng' => (float) env('PETSHOP_LNG', 106.8456),
    'pickup_free_radius_km' => (float) env('PICKUP_FREE_RADIUS_KM', 3),
    'pickup_extra_fee_per_km' => (int) env('PICKUP_EXTRA_FEE_PER_KM', 5000),

    // Geocoding (Nominatim / OpenStreetMap)
    'geocoding_user_agent' => env('GEOCODING_USER_AGENT', 'PetshopApp/1.0 (local dev)'),
    'geocoding_timeout' => (int) env('GEOCODING_TIMEOUT', 5),

    // Upload file
    'upload_max_bytes' => (int) env('UPLOAD_MAX_BYTES', 2 * 1024 * 1024),
];
