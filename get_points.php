<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // Ajout du CORS si nécessaire
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Inclure le fichier de connexion
    require_once __DIR__ . '/db_connect.php';

    // Préparer et exécuter la requête
    $sql = "SELECT * FROM points_livraison ORDER BY nom_magasin ASC";  // Correction du nom de la table
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
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'horaires' => $row['horaires'],
            'code_point' => $row['code_point']
        ];
    }

    // Fermer le résultat
    $result->close();

    // Envoyer la réponse avec success
    echo json_encode([
        'success' => true,
        'data' => $points
    ]);

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
