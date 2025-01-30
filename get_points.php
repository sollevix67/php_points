<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Connexion à la base de données
    $db = new PDO('mysql:host=192.168.1.61;dbname=livraison_db', 'vinted', 's24EJIlOje');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    if (!empty($search)) {
        // Recherche avec un terme spécifique
        $searchTerm = "%{$search}%";
        
        $query = $db->prepare("
            SELECT * FROM points_livraison 
            WHERE code_point LIKE :search 
            OR LOWER(nom_magasin) LIKE LOWER(:search)
            OR LOWER(adresse) LIKE LOWER(:search)
            OR code_postal LIKE :search
            OR LOWER(ville) LIKE LOWER(:search)
            ORDER BY nom_magasin ASC
        ");
        
        $query->execute([':search' => $searchTerm]);
    } else {
        // Récupérer tous les points
        $query = $db->query("SELECT * FROM points_livraison ORDER BY nom_magasin ASC");
    }
    
    $points = $query->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les horaires pour chaque point
    foreach ($points as &$point) {
        if (isset($point['horaires'])) {
            $point['horaires'] = trim($point['horaires']);
        } else {
            $point['horaires'] = '';
        }
    }
    
    // Envoyer la réponse
    echo json_encode($points);
    
} catch (PDOException $e) {
    // Log de l'erreur
    error_log("Erreur PDO: " . $e->getMessage());
    
    // Envoyer une réponse d'erreur
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
    
} catch (Exception $e) {
    // Log de l'erreur
    error_log("Erreur: " . $e->getMessage());
    
    // Envoyer une réponse d'erreur
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 
