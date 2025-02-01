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
    
    // Vérifier si le code_point est fourni
    if (!isset($_GET['code_point'])) {
        throw new ValidationException("Code point non fourni");
    }
    
    $code_point = trim($_GET['code_point']);
    
    // Vérifier si le point existe avant la suppression
    $checkQuery = $db->prepare('SELECT 1 FROM points_livraison WHERE code_point = ?');
    $checkQuery->execute([$code_point]);
    if (!$checkQuery->fetch()) {
        throw new ValidationException("Point non trouvé");
    }
    
    $db->beginTransaction();
    
    try {
        $query = $db->prepare('DELETE FROM points_livraison WHERE code_point = ?');
        $query->execute([$code_point]);
        
        $db->commit();
        
        Logger::log('info', 'Point supprimé', ['code_point' => $code_point]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Point supprimé avec succès'
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (ValidationException $e) {
    Logger::log('error', 'Erreur de validation lors de la suppression', [
        'message' => $e->getMessage(),
        'code_point' => $_GET['code_point'] ?? null
    ]);
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    Logger::log('error', 'Erreur lors de la suppression', [
        'message' => $e->getMessage(),
        'code_point' => $_GET['code_point'] ?? null
    ]);
    http_response_code(500);
    echo json_encode(['error' => 'Une erreur est survenue']);
}