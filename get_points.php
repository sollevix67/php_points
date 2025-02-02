<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Inclure le fichier de connexion
    require_once __DIR__ . '/db_connect.php';

    // Préparer et exécuter la requête
    $sql = "SELECT * FROM points ORDER BY nom_magasin ASC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Erreur lors de la récupération des points");
    }

    // Récupérer tous les points
    $points = [];
    while ($row = $result->fetch_assoc()) {
        $points[] = $row;
    }

    // Fermer le résultat
    $result->close();

    // Envoyer la réponse
    echo json_encode($points);

} catch (Exception $e) {
    // Log l'erreur
    error_log("Erreur get_points : " . $e->getMessage() . " [" . date('Y-m-d H:i:s') . "]", 3, __DIR__ . '/logs/app_errors.log');
    
    // Envoyer une réponse d'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des points'
    ]);
}
?> 
