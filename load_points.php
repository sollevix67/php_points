<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Utiliser config.php pour la connexion
    require_once 'config.php';
    $config = require 'config.php';
    $db = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['name']}", 
        $config['db']['user'], 
        $config['db']['pass']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = $db->query('SELECT * FROM points_livraison');
    $points = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($points);
} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erreur de base de données',
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
}
?>
