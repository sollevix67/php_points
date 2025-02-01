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
    
    $code_point = isset($_GET['code_point']) ? htmlspecialchars($_GET['code_point']) : null;
    
    if (!$code_point) {
        throw new Exception('Code point non fourni');
    }

    $query = $db->prepare('SELECT * FROM points_livraison WHERE code_point = ?');
    $query->execute([$code_point]);
    $point = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$point) {
        throw new Exception('Point non trouvé');
    }

    echo json_encode($point);
    
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
