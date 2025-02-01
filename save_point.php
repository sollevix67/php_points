<?php
require_once 'auth_middleware.php';
AuthMiddleware::checkAuth();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define('REQUIRED_FIELDS', ['type_point', 'nom_magasin', 'adresse', 'code_postal', 'ville', 'latitude', 'longitude']);

// Validation des entrées utilisateur
function validateInput($data) {
    // Sanitizer les entrées
    $data = array_map('trim', $data);
    $data = array_map('htmlspecialchars', $data);
    
    foreach (REQUIRED_FIELDS as $field) {
        if (empty($data[$field])) {
            throw new Exception("Le champ '$field' est requis");
        }
    }
    
    // Validation plus stricte des coordonnées GPS
    if (!is_numeric($data['latitude']) || $data['latitude'] < -90 || $data['latitude'] > 90) {
        throw new Exception("Latitude invalide");
    }
    if (!is_numeric($data['longitude']) || $data['longitude'] < -180 || $data['longitude'] > 180) {
        throw new Exception("Longitude invalide");
    }
    
    return $data;
}

try {
    // Remplacer la connexion en dur par config.php
    require_once 'config.php';
    $config = require 'config.php';
    $db = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['name']}", 
        $config['db']['user'], 
        $config['db']['pass']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Debug: afficher les données reçues
    error_log("Données POST reçues: " . print_r($_POST, true));
    
    $data = $_POST;
    
    validateInput($data);
    
    // Si horaires n'est pas défini, mettre une chaîne vide
    $data['horaires'] = isset($data['horaires']) ? $data['horaires'] : '';
    
    if (empty($data['code_point'])) {
        throw new Exception("Le code point est requis");
    }
    
    $code_point = $data['code_point'];
    
    // Debug: afficher la requête
    error_log("Requête INSERT avec code_point: $code_point");
    
    // Nouveau point
    $query = $db->prepare('
        INSERT INTO points_livraison 
        (code_point, type_point, nom_magasin, adresse, code_postal, ville, latitude, longitude, horaires) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    
    $params = [
        $code_point,
        $data['type_point'],
        $data['nom_magasin'],
        $data['adresse'],
        $data['code_postal'],
        $data['ville'],
        $data['latitude'],
        $data['longitude'],
        $data['horaires']
    ];
    
    // Debug: afficher les paramètres
    error_log("Paramètres: " . print_r($params, true));
    
    $db->beginTransaction();
    try {
        $query->execute($params);
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Point ajouté avec succès', 'code_point' => $code_point]);
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => 'Une erreur est survenue',
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
}
?>
