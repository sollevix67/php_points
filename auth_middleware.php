<?php
session_start();
require_once 'csrf.php';

class AuthMiddleware {
    public static function setSecurityHeaders() {
        // Protection contre le clickjacking
        header('X-Frame-Options: DENY');
        
        // Protection contre le MIME-sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Active la protection XSS intégrée aux navigateurs modernes
        header('X-XSS-Protection: 1; mode=block');
        
        // Politique de sécurité du contenu (CSP)
        $cspHeader = "Content-Security-Policy: ".
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com https://maps.googleapis.com https://cdn.jsdelivr.net; " .
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
            "img-src 'self' data: https://*.googleapis.com https://*.gstatic.com; " .
            "connect-src 'self' https://*.googleapis.com https://www.sollevix.ovh; " .
            "frame-src 'self' https://www.google.com https://*.googleapis.com; " .
            "font-src 'self' https://cdnjs.cloudflare.com;";
        header($cspHeader);
        
        // Strict Transport Security (forcer HTTPS)
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        
        // Référer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions Policy (anciennement Feature-Policy)
        header('Permissions-Policy: geolocation=(self), camera=(), microphone=()');
    }

    public static function checkAuth() {
        // Ajouter les en-têtes de sécurité au début
        self::setSecurityHeaders();
        
        try {
            if (!isset($_SESSION['auth_token']) || !isset($_SESSION['user_id'])) {
                throw new AuthenticationException('Non autorisé');
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
        } catch (Exception $e) {
            self::handleError($e);
        }
    }

    private static function handleError(Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}
?> 