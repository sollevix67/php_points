<?php
session_start();
header('Content-Type: application/json');
require_once 'csrf.php';
require_once 'auth_middleware.php';

try {
    AuthMiddleware::checkAuth();
    $token = CSRF::generateToken();
    echo json_encode(['token' => $token]);
} catch (Exception $e) {
    http_response_code(403);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?> 