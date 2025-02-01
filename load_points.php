<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Définir la constante DEBUG
define('DEBUG', true);

try {
    // Configuration directe de la base de données
    $config = [
        'db' => [
            'host' => '192.168.1.61',
            'name' => 'livraison_db',
            'user' => 'vinted',
            'pass' => 's24EJIlOje'
        ]
    ];

    // Vérification des paramètres de configuration
    if (!isset($config['db']) || 
        !isset($config['db']['host']) || 
        !isset($config['db']['name']) || 
        !isset($config['db']['user']) || 
        !isset($config['db']['pass'])) {
        throw new Exception('Configuration de base de données invalide');
    }

    // Création de la connexion PDO avec gestion des erreurs
    try {
        $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset=utf8mb4";
        $db = new PDO(
            $dsn, 
            $config['db']['user'], 
            $config['db']['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );
    } catch (PDOException $e) {
        error_log("Erreur de connexion PDO: " . $e->getMessage());
        throw new Exception('Impossible de se connecter à la base de données: ' . $e->getMessage());
    }
    
    // Requête pour récupérer les points
    $query = $db->query('
        SELECT 
            code_point,
            type_point,
            nom_magasin,
            adresse,
            code_postal,
            ville,
            latitude,
            longitude,
            horaires
        FROM points_livraison
        ORDER BY nom_magasin ASC
    ');
    
    $points = $query->fetchAll();
    
    // Nettoyer les données avant de les envoyer
    foreach ($points as &$point) {
        $point['horaires'] = trim($point['horaires'] ?? '');
        $point['latitude'] = floatval($point['latitude']);
        $point['longitude'] = floatval($point['longitude']);
    }
    
    echo json_encode($points, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erreur de base de données',
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
}
?>
