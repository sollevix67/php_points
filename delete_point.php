<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

/**
 * Classe de gestion des points de livraison
 * @package LiveTrack
 */
class PointLivraison {
    /**
     * Crée un nouveau point de livraison
     * @param array $data Les données du point
     * @return array Résultat de l'opération
     * @throws ValidationException Si les données sont invalides
     */
    public function create($data) {
        // ... implémentation
    }
}

class PointLivraisonValidator {
    private const REQUIRED_FIELDS = ['type_point', 'nom_magasin', 'adresse', 'code_postal', 'ville', 'latitude', 'longitude'];
    
    public static function validate($data) {
        // Vérification des champs requis
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                throw new ValidationException("Le champ '$field' est requis");
            }
        }
        
        // Validation du code postal
        if (!preg_match('/^[0-9]{5}$/', $data['code_postal'])) {
            throw new ValidationException("Format de code postal invalide");
        }
        
        // Validation des coordonnées GPS
        if (!is_numeric($data['latitude']) || !is_numeric($data['longitude'])) {
            throw new ValidationException("Coordonnées GPS invalides");
        }
        
        if ($data['latitude'] < -90 || $data['latitude'] > 90) {
            throw new ValidationException("Latitude invalide");
        }
        
        if ($data['longitude'] < -180 || $data['longitude'] > 180) {
            throw new ValidationException("Longitude invalide");
        }
        
        // Ajouter validation du format des horaires
        if (!empty($data['horaires'])) {
            $horaires = explode("\n", $data['horaires']);
            foreach ($horaires as $horaire) {
                if (!preg_match('/^[A-Za-z]+:\s+\d{1,2}h\d{2}\s+-\s+\d{1,2}h\d{2}$/', trim($horaire))) {
                    throw new ValidationException("Format d'horaire invalide");
                }
            }
        }
        
        return true;
    }
}

// Ajouter une classe Logger
class Logger {
    public static function log($type, $message, $context = []) {
        $logFile = __DIR__ . '/logs/' . date('Y-m-d') . '.log';
        $logEntry = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($type),
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}

try {
    $db = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    $data = array_map('trim', $_POST);
    
    // Validation des données
    PointLivraisonValidator::validate($data);
    
    // Vérification du code_point existant
    $checkQuery = $db->prepare('SELECT 1 FROM points_livraison WHERE code_point = ?');
    $checkQuery->execute([$data['code_point']]);
    if ($checkQuery->fetch()) {
        throw new ValidationException("Ce code point existe déjà");
    }
    
    $db->beginTransaction();
    
    try {
        $query = $db->prepare('
            INSERT INTO points_livraison 
            (code_point, type_point, nom_magasin, adresse, code_postal, ville, latitude, longitude, horaires) 
            VALUES (:code_point, :type_point, :nom_magasin, :adresse, :code_postal, :ville, :latitude, :longitude, :horaires)
        ');
        
        $query->execute([
            ':code_point' => $data['code_point'],
            ':type_point' => $data['type_point'],
            ':nom_magasin' => $data['nom_magasin'],
            ':adresse' => $data['adresse'],
            ':code_postal' => $data['code_postal'],
            ':ville' => $data['ville'],
            ':latitude' => $data['latitude'],
            ':longitude' => $data['longitude'],
            ':horaires' => $data['horaires'] ?? ''
        ]);
        
        $db->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Point ajouté avec succès',
            'code_point' => $data['code_point']
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (ValidationException $e) {
    error_log("Erreur de validation: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Une erreur est survenue']);
}