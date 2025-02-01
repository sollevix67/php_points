<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'db_connect.php';

    // Vérification que toutes les données requises sont présentes
    $requiredFields = ['type_point', 'nom_magasin', 'adresse', 'code_postal', 'ville', 'latitude', 'longitude', 'horaires', 'code_point'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Le champ $field est requis");
        }
    }

    // Récupérer les données du formulaire
    $isEdit = isset($_POST['isEdit']) && $_POST['isEdit'] === 'true';
    $originalCodePoint = $_POST['original_code_point'] ?? null;
    
    $type_point = $_POST['type_point'];
    $nom_magasin = $_POST['nom_magasin'];
    $adresse = $_POST['adresse'];
    $code_postal = $_POST['code_postal'];
    $ville = $_POST['ville'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $horaires = $_POST['horaires'];
    $code_point = $_POST['code_point'];

    // Connexion à la base de données
    if (!$conn) {
        throw new Exception("Erreur de connexion à la base de données");
    }

    if ($isEdit) {
        if (!$originalCodePoint) {
            throw new Exception("Code point original manquant pour la modification");
        }

        // Modification d'un point existant
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
            throw new Exception("Erreur de préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("sssssddsss", 
            $type_point, 
            $nom_magasin, 
            $adresse, 
            $code_postal, 
            $ville, 
            $latitude, 
            $longitude, 
            $horaires, 
            $code_point,
            $originalCodePoint
        );
    } else {
        // Création d'un nouveau point
        $sql = "INSERT INTO points_livraison (type_point, nom_magasin, adresse, code_postal, ville, latitude, longitude, horaires, code_point) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("sssssddsss", 
            $type_point, 
            $nom_magasin, 
            $adresse, 
            $code_postal, 
            $ville, 
            $latitude, 
            $longitude, 
            $horaires, 
            $code_point
        );
    }

    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de l'exécution de la requête : " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Envoyer une réponse JSON valide
    echo json_encode([
        'success' => true,
        'message' => $isEdit ? 'Point modifié avec succès' : 'Point créé avec succès'
    ]);

} catch (Exception $e) {
    // S'assurer que la réponse d'erreur est un JSON valide
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
