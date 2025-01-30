<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = new PDO('mysql:host=192.168.1.61;dbname=livraison_db', 'vinted', 's24EJIlOje');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Debug: afficher les données reçues
    error_log("Données POST reçues: " . print_r($_POST, true));
    
    $data = $_POST;
    
    // Vérifier que les champs requis sont présents
    $required_fields = ['type_point', 'nom_magasin', 'adresse', 'code_postal', 'ville', 'latitude', 'longitude'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Le champ '$field' est requis");
        }
    }
    
    // Si horaires n'est pas défini, mettre une chaîne vide
    $data['horaires'] = isset($data['horaires']) ? $data['horaires'] : '';
    
    if (empty($data['code_point'])) {
        throw new Exception("Le code point est requis");
    }
    
    $code_point = $data['code_point'];
    
    // Debug: afficher la requête
    error_log("Requête INSERT avec code_point: $code_point");
    
    // Nouveau point
    $query = $db->prepare('
        INSERT INTO points_livraison 
        (code_point, type_point, nom_magasin, adresse, code_postal, ville, latitude, longitude, horaires) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    
    $params = [
        $code_point,
        $data['type_point'],
        $data['nom_magasin'],
        $data['adresse'],
        $data['code_postal'],
        $data['ville'],
        $data['latitude'],
        $data['longitude'],
        $data['horaires']
    ];
    
    // Debug: afficher les paramètres
    error_log("Paramètres: " . print_r($params, true));
    
    $query->execute($params);
    
    echo json_encode(['success' => true, 'message' => 'Point ajouté avec succès', 'code_point' => $code_point]);
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
