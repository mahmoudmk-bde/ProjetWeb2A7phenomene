<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'projetweb';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

function secure_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('project_base_uri')) {
    function project_base_uri() {
        static $base = null;
        if ($base !== null) {
            return $base;
        }
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            $base = '';
            return $base;
        }
        $base = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])));
        $base = str_replace('\\', '/', $base);
        $base = rtrim($base, '/');
        if ($base === '.' || $base === '/') {
            $base = '';
        }
        return $base;
    }
}

if (!function_exists('prepend_project_base')) {
    function prepend_project_base($path) {
        $path = ltrim($path, '/');
        $base = project_base_uri();
        $prefix = $base ? $base . '/' : '/';
        return $prefix . $path;
    }
}

if (!function_exists('normalize_asset_path')) {
    function normalize_asset_path($path, $default = 'img/favicon.png') {
        if (empty($path)) {
            return $default;
        }
        $path = trim($path);
        $base = project_base_uri();
        $prefix = $base ? $base . '/' : '/';

        if (stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0) {
            return $path;
        }

        $normalized = null;
        if (preg_match('#uploads/events/[^"\']+#', $path, $match)) {
            $normalized = $match[0];
        } elseif (strpos($path, '/gamingroom/') === 0) {
            $normalized = ltrim(substr($path, strlen('/gamingroom/')), '/');
        } elseif ($path[0] === '/') {
            $normalized = ltrim($path, '/');
        } else {
            $normalized = ltrim($path, '/');
        }

        if (!$normalized) {
            return $default;
        }

        return $prefix . $normalized;
    }
}
?>