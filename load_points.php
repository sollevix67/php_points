<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Inclure le logger
require_once __DIR__ . '/logger.php';

try {
    // Inclure le fichier de connexion
    require_once __DIR__ . '/db_connect.php';
    
    // Préparer et exécuter la requête
    $sql = "SELECT * FROM points_livraison ORDER BY nom_magasin ASC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Erreur lors de la récupération des points");
    }

    // Récupérer tous les points
    $points = [];
    while ($row = $result->fetch_assoc()) {
        $points[] = [
            'type_point' => $row['type_point'],
            'nom_magasin' => $row['nom_magasin'],
            'adresse' => $row['adresse'],
            'code_postal' => $row['code_postal'],
            'ville' => $row['ville'],
            'latitude' => floatval($row['latitude']),
            'longitude' => floatval($row['longitude']),
            'horaires' => $row['horaires'],
            'code_point' => $row['code_point']
        ];
    }

    // Fermer le résultat
    $result->close();

    // Log du succès
    Logger::log("Points chargés avec succès. Nombre de points : " . count($points), 'info');

    // Envoyer la réponse
    echo json_encode([
        'success' => true,
        'data' => $points
    ]);

} catch (Exception $e) {
    // Log de l'erreur
    Logger::log("Erreur load_points : " . $e->getMessage());
    
    // Envoyer une réponse d'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors du chargement des points'
    ]);
}
?>
