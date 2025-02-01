<?php
// Utilisation de variables d'environnement pour sécuriser les informations de connexion
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'default_db',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: ''
    ],
    'security' => [
        'max_login_attempts' => 5,
        'lockout_time' => 900,
        'token_expiry' => 3600
    ]
]; 