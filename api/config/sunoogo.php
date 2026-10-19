<?php

return [

    /*
    | Pays de lancement : Burkina Faso. Les numéros d'urgence sont en base
    | (table emergency_contacts) et modifiables depuis le back-office.
    */
    'default_country' => env('SUNOOGO_COUNTRY', 'BF'),

    'currency' => 'XOF',

    /*
    | Détection de détresse dans les messages des membres. Une correspondance
    | "high" déclenche immédiatement une alerte superviseur et l'affichage des
    | numéros d'urgence. Liste à faire valider par un clinicien.
    */
    'risk_keywords' => [
        'high' => [
            'suicide', 'me suicider', 'me tuer', 'mettre fin à mes jours', 'en finir',
            'plus envie de vivre', 'mourir', 'me pendre', 'me faire du mal',
            'je veux disparaître', 'tuer quelqu\'un',
        ],
        'medium' => [
            'désespoir', 'désespéré', 'désespérée', 'je n\'en peux plus', 'à bout',
            'personne ne m\'aime', 'je suis un fardeau', 'automutilation', 'scarification',
            'il me frappe', 'elle me frappe', 'violence',
        ],
    ],

    'payments' => [
        // mock | wave | orange_money | paypal
        'default' => env('PAYMENT_PROVIDER', 'mock'),

        'success_url' => env('PAYMENT_SUCCESS_URL', env('APP_URL').'/paiement/succes'),
        'error_url' => env('PAYMENT_ERROR_URL', env('APP_URL').'/paiement/echec'),

        'wave' => [
            'api_key' => env('WAVE_API_KEY'),
            'webhook_secret' => env('WAVE_WEBHOOK_SECRET'),
            'base_url' => env('WAVE_BASE_URL', 'https://api.wave.com'),
        ],

        'orange_money' => [
            'client_id' => env('ORANGE_MONEY_CLIENT_ID'),
            'client_secret' => env('ORANGE_MONEY_CLIENT_SECRET'),
            'merchant_key' => env('ORANGE_MONEY_MERCHANT_KEY'),
            'base_url' => env('ORANGE_MONEY_BASE_URL', 'https://api.orange.com'),
            'webpay_path' => env('ORANGE_MONEY_WEBPAY_PATH', '/orange-money-webpay/bf/v1/webpayment'),
            'currency' => env('ORANGE_MONEY_CURRENCY', 'XOF'),
        ],
    ],
];
