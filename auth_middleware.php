<?php
session_start();
require_once 'csrf.php';

class AuthMiddleware {
    public static function checkAuth() {
        // Vérification de l'authentification
        if (!isset($_SESSION['auth_token']) || !isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'error' => true,
                'message' => 'Non autorisé'
            ]);
            exit;
        }

        // Vérification CSRF pour les requêtes POST, PUT, DELETE
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) {
            $headers = getallheaders();
            $csrfToken = isset($headers['X-CSRF-Token']) ? $headers['X-CSRF-Token'] : null;
            
            if (!$csrfToken) {
                http_response_code(403);
                echo json_encode([
                    'error' => true,
                    'message' => 'Token CSRF manquant'
                ]);
                exit;
            }

            try {
                CSRF::verifyToken($csrfToken);
            } catch (Exception $e) {
                http_response_code(403);
                echo json_encode([
                    'error' => true,
                    'message' => 'Token CSRF invalide'
                ]);
                exit;
            }
        }
    }
}
?> 