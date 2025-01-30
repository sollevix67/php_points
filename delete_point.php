<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = new PDO('mysql:host=192.168.1.61;dbname=livraison_db', 'vinted', 's24EJIlOje');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $code_point = isset($_GET['code_point']) ? $_GET['code_point'] : null;
    
    if ($code_point) {
        $query = $db->prepare('DELETE FROM points_livraison WHERE code_point = ?');
        $query->execute([$code_point]);
        
        echo json_encode(['success' => true, 'message' => 'Point supprimé avec succès']);
    } else {
        echo json_encode(['error' => 'Code point non fourni']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}