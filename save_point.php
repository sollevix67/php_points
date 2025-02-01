<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Connexion à la base de données
    $db = new PDO('mysql:host=192.168.1.61;dbname=livraison_db', 'vinted', 's24EJIlOje');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
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

    if ($isEdit) {
        // Modification d'un point existant
        $sql = "UPDATE points SET 
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
        $sql = "INSERT INTO points (type_point, nom_magasin, adresse, code_postal, ville, latitude, longitude, horaires, code_point) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
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

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Erreur lors de la sauvegarde : " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
