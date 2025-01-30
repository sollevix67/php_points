<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = new PDO('mysql:host=192.168.1.61;dbname=livraison_db', 'vinted', 's24EJIlOje');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = $db->query('SELECT * FROM points_livraison');
    $points = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($points);
} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
