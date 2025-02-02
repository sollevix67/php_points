<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Inclure le fichier de connexion
    require_once __DIR__ . '/db_connect.php';

    // Vérifier si le code_point est fourni
    if (!isset($_GET['code_point']) || empty($_GET['code_point'])) {
        throw new Exception("Code point non fourni");
    }

    $code_point = htmlspecialchars(trim($_GET['code_point']));

    // Vérifier si le point existe
    $checkStmt = $conn->prepare("SELECT code_point FROM points WHERE code_point = ?");
    $checkStmt->bind_param("s", $code_point);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Point non trouvé");
    }
    
    $checkStmt->close();

    // Préparer et exécuter la suppression
    $stmt = $conn->prepare("DELETE FROM points WHERE code_point = ?");
    if (!$stmt) {
        throw new Exception("Erreur de préparation de la requête");
    }

    $stmt->bind_param("s", $code_point);
    
    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de la suppression");
    }

    // Fermer la requête
    $stmt->close();

    // Envoyer une réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Point supprimé avec succès'
    ]);

} catch (Exception $e) {
    // Log l'erreur
    error_log("Erreur delete_point : " . $e->getMessage() . " [" . date('Y-m-d H:i:s') . "]", 3, __DIR__ . '/logs/app_errors.log');
    
    // Envoyer une réponse d'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>