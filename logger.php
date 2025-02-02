<?php
class Logger {
    private static $logDir;
    
    public static function init() {
        // Définir le répertoire des logs
        self::$logDir = __DIR__ . '/logs';
        
        // Créer le répertoire s'il n'existe pas
        if (!file_exists(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }
    
    public static function log($message, $type = 'error') {
        // Initialiser si pas déjà fait
        if (!isset(self::$logDir)) {
            self::init();
        }
        
        $logFile = self::$logDir . '/' . $type . '_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] $message" . PHP_EOL;
        
        // Essayer d'écrire dans le fichier de log
        if (!@error_log($formattedMessage, 3, $logFile)) {
            // Si échec, écrire dans le log système
            error_log("Erreur application: $message");
        }
    }
}

// Initialiser le logger
Logger::init();
?> 