<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // Ajout du CORS
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Inclure le logger
require_once __DIR__ . '/logger.php';

try {
    // Inclure le fichier de connexion
    require_once __DIR__ . '/db_connect.php';

    // Debug log
    Logger::log("Données reçues : " . print_r($_POST, true), 'debug');

    // Vérification que toutes les données requises sont présentes
    $requiredFields = ['type_point', 'nom_magasin', 'adresse', 'code_postal', 'ville', 'latitude', 'longitude', 'horaires', 'code_point'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field])) {
            throw new Exception("Le champ $field est manquant");
        }
    }

    // Nettoyer et valider les données
    $data = [];
    foreach ($requiredFields as $field) {
        $data[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : '';
        // Validation spécifique pour les coordonnées
        if (in_array($field, ['latitude', 'longitude'])) {
            $data[$field] = floatval($data[$field]);
        }
    }

    // Récupérer les données supplémentaires
    $isEdit = isset($_POST['isEdit']) && $_POST['isEdit'] === 'true';
    $originalCodePoint = $_POST['original_code_point'] ?? null;

    Logger::log("Mode édition : " . ($isEdit ? "oui" : "non") . ", Code point original : " . $originalCodePoint, 'debug');

    // Préparer la requête SQL appropriée
    if ($isEdit && $originalCodePoint) {
        $sql = "UPDATE points_livraison SET 
                type_point = ?, 
                nom_magasin = ?, 
                adresse = ?, 
                code_postal = ?, 
                ville = ?, 
                latitude = ?, 
                longitude = ?, 
                horaires = ?, 
                code_point = ?
                WHERE code_point = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête UPDATE : " . $conn->error);
        }

        $stmt->bind_param("sssssddsss", 
            $data['type_point'],
            $data['nom_magasin'],
            $data['adresse'],
            $data['code_postal'],
            $data['ville'],
            $data['latitude'],
            $data['longitude'],
            $data['horaires'],
            $data['code_point'],
            $originalCodePoint
        );
    } else {
        // Vérifier si le code_point existe déjà
        $checkStmt = $conn->prepare("SELECT code_point FROM points_livraison WHERE code_point = ?");
        if (!$checkStmt) {
            throw new Exception("Erreur de préparation de la requête de vérification : " . $conn->error);
        }

        $checkStmt->bind_param("s", $data['code_point']);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        if ($result->num_rows > 0) {
            $checkStmt->close();
            throw new Exception("Un point avec ce code existe déjà");
        }
        $checkStmt->close();

        $sql = "INSERT INTO points_livraison (type_point, nom_magasin, adresse, code_postal, ville, latitude, longitude, horaires, code_point) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête INSERT : " . $conn->error);
        }

        $stmt->bind_param("sssssddsss", 
            $data['type_point'],
            $data['nom_magasin'],
            $data['adresse'],
            $data['code_postal'],
            $data['ville'],
            $data['latitude'],
            $data['longitude'],
            $data['horaires'],
            $data['code_point']
        );
    }

    // Exécuter la requête
    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de l'exécution de la requête : " . $stmt->error);
    }

    // Log du succès
    Logger::log("Point " . ($isEdit ? "modifié" : "créé") . " avec succès : " . $data['code_point'], 'info');

    // Fermer la requête
    $stmt->close();

    // Envoyer une réponse de succès
    echo json_encode([
        'success' => true,
        'message' => $isEdit ? 'Point modifié avec succès' : 'Point créé avec succès',
        'code_point' => $data['code_point']
    ]);

} catch (Exception $e) {
    Logger::log("Erreur save_point : " . $e->getMessage());
    
    // Envoyer une réponse d'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // La connexion sera fermée automatiquement grâce au register_shutdown_function dans db_connect.php
}
?>
