<?php
// Utilisation de variables d'environnement pour sécuriser les informations de connexion
return [
    'db' => [
        'host' => getenv('DB_HOST'),
        'name' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'pass' => getenv('DB_PASS')
    ]
]; 