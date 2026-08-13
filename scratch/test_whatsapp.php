<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$waService = new \App\Services\WhatsAppService();
$response = $waService->sendTemplateMessage(
    to: '919999999999', // Dummy number
    templateName: 'chandla_added',
    languageCode: 'en',
    components: [
        [
            'type' => 'body',
            'parameters' => [
                \App\Services\WhatsAppService::formatTextParameter('Test Giver'),
                \App\Services\WhatsAppService::formatTextParameter('200')
            ]
        ]
    ]
);

echo "Response:\n";
print_r($response);
