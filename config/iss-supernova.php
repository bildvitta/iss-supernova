<?php

return [
    'base_uri' => env('MS_SUPERNOVA_BASE_URI'),
    'prefix' => env('MS_SUPERNOVA_API_PREFIX', '/api'),
    'companies' => explode(',', env('MS_SUPERNOVA_COMPANIES', 'ebde8a05-fe11-44ad-9b3a-39dee841c83b,a9d94282-4120-4de3-b25c-9fa9a41a483f,334f94b4-5457-4b12-a2ec-3472434363af')),
];
