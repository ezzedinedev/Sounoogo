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
        // mock | moneyfusion | paypal
        'default' => env('PAYMENT_PROVIDER', 'mock'),

        'success_url' => env('PAYMENT_SUCCESS_URL', env('APP_URL').'/paiement/succes'),
        'error_url' => env('PAYMENT_ERROR_URL', env('APP_URL').'/paiement/echec'),

        // MoneyFusion : « lien API » de l'application, généré dans le tableau de bord marchand.
        'moneyfusion' => [
            'api_url' => env('MONEYFUSION_API_URL'),
            'status_url' => env('MONEYFUSION_STATUS_URL', 'https://pay.moneyfusion.net/paiementNotif'),
        ],
    ],
];
