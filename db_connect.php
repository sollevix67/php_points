<?php
// Paramètres de connexion à la base de données
$host = '192.168.1.61';         // L'hôte de la base de données
$dbname = 'livraison_db';       // Le nom de la base de données
$username = 'vinted';           // Le nom d'utilisateur MySQL
$password = 's24EJIlOje';       // Le mot de passe MySQL
$port = 3306;                   // Le port MySQL (par défaut: 3306)


// Création de la connexion
try {
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    // Vérification de la connexion
    if ($conn->connect_error) {
        throw new Exception("Échec de la connexion : " . $conn->connect_error);
    }

    // Configuration du jeu de caractères en UTF-8
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    // En cas d'erreur, renvoyer une réponse JSON avec l'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit();
}
?> 