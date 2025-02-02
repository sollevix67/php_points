<?php
// Désactiver l'affichage des erreurs en production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Charger les variables d'environnement depuis un fichier .env
function loadEnv() {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

// Charger les variables d'environnement
loadEnv();

// Paramètres de connexion sécurisés
$host = $_ENV['DB_HOST'] ?? '192.168.1.61';
$dbname = $_ENV['DB_NAME'] ?? 'livraison_db';
$username = $_ENV['DB_USER'] ?? 'vinted';
$password = $_ENV['DB_PASS'] ?? 's24EJIlOje';
$port = $_ENV['DB_PORT'] ?? 3306;

// Création de la connexion avec gestion des timeouts
try {
    // Configuration des options mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    $conn = new mysqli();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);  // Timeout de connexion de 10 secondes
    
    // Connexion avec SSL si disponible
    if (isset($_ENV['DB_SSL']) && $_ENV['DB_SSL'] === 'true') {
        $conn->ssl_set(
            $_ENV['DB_SSL_KEY'] ?? null,
            $_ENV['DB_SSL_CERT'] ?? null,
            $_ENV['DB_SSL_CA'] ?? null,
            $_ENV['DB_SSL_CAPATH'] ?? null,
            $_ENV['DB_SSL_CIPHER'] ?? null
        );
    }
    
    $conn->real_connect($host, $username, $password, $dbname, $port);

    // Configuration sécurisée
    $conn->set_charset("utf8mb4");
    $conn->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
    $conn->query("SET SESSION wait_timeout = 600");  // Timeout de 10 minutes
    
    // Vérification de la connexion
    if ($conn->connect_error) {
        throw new Exception("Erreur de connexion à la base de données");
    }

} catch (Exception $e) {
    // Log l'erreur dans un fichier
    error_log("Erreur DB : " . $e->getMessage() . " [" . date('Y-m-d H:i:s') . "]", 3, __DIR__ . '/logs/db_errors.log');
    
    // Réponse générique pour la production
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue lors de la connexion à la base de données'
    ]);
    exit();
}

// Fonction de nettoyage à la fermeture du script
register_shutdown_function(function() use ($conn) {
    if ($conn) {
        $conn->close();
    }
});
?> 