<?php

return [
    'credentials_path' => env('FIREBASE_CREDENTIALS_PATH', storage_path('app/firebase-credentials.json')),
    'project_id' => env('FIREBASE_PROJECT_ID'),
];

