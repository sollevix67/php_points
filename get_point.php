



<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = new PDO('mysql:host=192.168.1.61;dbname=livraison_db', 'vinted', 's24EJIlOje');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $code_point = isset($_GET['code_point']) ? $_GET['code_point'] : null;
    
    if (!$code_point) {
        throw new Exception('Code point non fourni');
    }

    $query = $db->prepare('
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
        WHERE code_point = ?
    ');
    
    $query->execute([$code_point]);
    $point = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$point) {
        throw new Exception('Point non trouvé');
    }

    // Nettoyer les horaires
    if (isset($point['horaires'])) {
        $point['horaires'] = trim($point['horaires']);
    } else {
        $point['horaires'] = '';
    }
    
    echo json_encode($point);
    
} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} 