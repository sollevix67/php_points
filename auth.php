<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

class Auth {
    private $db;
    private $maxLoginAttempts = 5;
    private $lockoutTime = 900; // 15 minutes
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function login($username, $password) {
        try {
            $query = $this->db->prepare('SELECT id, username, password_hash FROM admins WHERE username = ?');
            $query->execute([$username]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Générer un token de session unique
                $token = bin2hex(random_bytes(32));
                
                // Stocker le token en session
                $_SESSION['auth_token'] = $token;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                return [
                    'success' => true,
                    'token' => $token
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Identifiants invalides'
            ];
            
        } catch (Exception $e) {
            error_log("Erreur d'authentification: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'authentification'
            ];
        }
    }
    
    public function checkAuth() {
        if (!isset($_SESSION['auth_token']) || !isset($_SESSION['user_id'])) {
            return false;
        }
        return true;
    }
    
    public function logout() {
        session_destroy();
        return ['success' => true];
    }

    public function checkLoginAttempts($username) {
        // Vérifier les tentatives de connexion
    }
}

// Point d'entrée API
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $config = require 'config.php';
        $db = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']}", 
            $config['db']['user'], 
            $config['db']['pass']
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $auth = new Auth($db);
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        switch($data['action']) {
            case 'login':
                if (!isset($data['username']) || !isset($data['password'])) {
                    throw new Exception('Données manquantes');
                }
                $result = $auth->login($data['username'], $data['password']);
                echo json_encode($result);
                break;
                
            case 'logout':
                $result = $auth->logout();
                echo json_encode($result);
                break;
                
            case 'check':
                $result = ['authenticated' => $auth->checkAuth()];
                echo json_encode($result);
                break;
                
            default:
                throw new Exception('Action non reconnue');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?> 