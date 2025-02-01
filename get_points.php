<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Définir la constante DEBUG
define('DEBUG', true);

try {
    // Configuration de la base de données
    $config = [
        'db' => [
            'host' => '192.168.1.61',
            'name' => 'livraison_db',
            'user' => 'vinted',
            'pass' => 's24EJIlOje',
            'charset' => 'utf8mb4'
        ]
    ];

    // Création de la connexion PDO
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}";
    $db = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['db']['charset']}"
    ]);

    // Requête pour récupérer tous les points
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

    // Nettoyer et formater les données
    foreach ($points as &$point) {
        // Convertir les coordonnées en nombres
        $point['latitude'] = floatval($point['latitude']);
        $point['longitude'] = floatval($point['longitude']);
        
        // Nettoyer les horaires
        $point['horaires'] = trim($point['horaires'] ?? '');
        
        // S'assurer que tous les champs texte sont bien encodés
        $point['nom_magasin'] = htmlspecialchars($point['nom_magasin']);
        $point['adresse'] = htmlspecialchars($point['adresse']);
        $point['ville'] = htmlspecialchars($point['ville']);
    }

    // Envoyer la réponse
    echo json_encode($points, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    // Log de l'erreur
    error_log("Erreur PDO: " . $e->getMessage());
    
    // Réponse d'erreur
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erreur de base de données',
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
} catch (Exception $e) {
    // Log de l'erreur
    error_log("Erreur: " . $e->getMessage());
    
    // Réponse d'erreur
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
}
?> 
