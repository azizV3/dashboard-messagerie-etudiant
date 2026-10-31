<?php
/*
 * One-time database installer.
 *   - Allowed from the command line (php setup.php) or from localhost only.
 *   - Refuses to run again once setup.lock exists.
 * Delete this file (or leave it locked) after installation.
 */
$isCli   = PHP_SAPI === 'cli';
$isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
$lock    = __DIR__ . '/setup.lock';

if (!$isCli && !$isLocal) {
    http_response_code(403);
    exit('Forbidden');
}
if (file_exists($lock)) {
    http_response_code(403);
    exit("Setup already done. Delete setup.lock to run it again.\n");
}

require_once __DIR__ . '/connect.php';

try {
    $conn->exec("
    CREATE TABLE IF NOT EXISTS students (
        id INT PRIMARY KEY AUTO_INCREMENT,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        university VARCHAR(150),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS tasks (
        id INT PRIMARY KEY AUTO_INCREMENT,
        student_id INT,
        title VARCHAR(255) NOT NULL,
        due_date DATE NOT NULL,
        priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
        description TEXT,
        status ENUM('Pending', 'Completed') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message_text TEXT NOT NULL,
        file_path VARCHAR(255) DEFAULT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id)   REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES students(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS music (
        id INT PRIMARY KEY AUTO_INCREMENT,
        titre VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL
    );
    ");

    // Demo tracks matching the files in /music (only if the table is empty).
    if ((int)$conn->query("SELECT COUNT(*) FROM music")->fetchColumn() === 0) {
        $ins = $conn->prepare("INSERT INTO music (titre, file_path) VALUES (?, ?)");
        $ins->execute(['Track 1', 'music/m.mp3']);
        $ins->execute(['Track 2', 'music/m2.mp3']);
    }

    file_put_contents($lock, date('c'));
    echo "Database ready. Locked with setup.lock.\n";
} catch (PDOException $e) {
    error_log('setup.php failed: ' . $e->getMessage());
    http_response_code(500);
    echo "Setup failed, check the server log.\n";
}
