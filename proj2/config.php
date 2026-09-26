<?php
$is_local = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1');

if ($is_local) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'proj_vibecoded');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    define('DB_HOST', 'mysql.ton-hebergeur.com'); 
    define('DB_NAME', 'u123456789_studentai');
    define('DB_USER', 'u123456789_admin');
    define('DB_PASS', 'TonMotDePasseSecurise');
}
?>