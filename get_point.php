<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Inclure le logger
require_once __DIR__ . '/logger.php';

try {
    // Inclure le fichier de connexion
    require_once __DIR__ . '/db_connect.php';

    // Vérifier si le code_point est fourni
    if (!isset($_GET['code_point']) || empty($_GET['code_point'])) {
        throw new Exception("Code point non fourni");
    }

    $code_point = htmlspecialchars(trim($_GET['code_point']));

    // Préparer et exécuter la requête
    $stmt = $conn->prepare("SELECT * FROM points_livraison WHERE code_point = ?");
    if (!$stmt) {
        throw new Exception("Erreur de préparation de la requête");
    }

    $stmt->bind_param("s", $code_point);
    
    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de l'exécution de la requête");
    }

    $result = $stmt->get_result();
    $point = $result->fetch_assoc();

    if (!$point) {
        throw new Exception("Point non trouvé");
    }

    // Fermer la requête
    $stmt->close();

    // Envoyer la réponse
    echo json_encode([
        'success' => true,
        'data' => $point
    ]);

} catch (Exception $e) {
    // Utiliser la nouvelle classe Logger
    Logger::log("Erreur get_point : " . $e->getMessage());
    
    // Envoyer une réponse d'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 