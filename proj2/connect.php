<?php
// Database connection. Credentials are NOT stored in the repo:
//   1. environment variables DB_HOST / DB_NAME / DB_USER / DB_PASS, or
//   2. config.local.php (copy of config.example.php, git-ignored).
$configFile = __DIR__ . '/config.local.php';
$cfg = is_file($configFile) ? require $configFile : [];

$host     = getenv('DB_HOST') ?: ($cfg['host'] ?? 'localhost');
$dbname   = getenv('DB_NAME') ?: ($cfg['name'] ?? 'etud');
$username = getenv('DB_USER') ?: ($cfg['user'] ?? 'root');
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($cfg['pass'] ?? '');

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Log the details server-side, show nothing sensitive to the visitor.
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Erreur de connexion à la base de données.');
}
