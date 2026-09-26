<?php
require_once 'connect.php';

try {
    $sql = "
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    
    $conn->exec($sql);
    echo "<h3 style='color:green; text-align:center;'>Database structured successfully for Study Platform!</h3>";
    echo "<p style='text-align:center;'><a href='register.php'>Go to Registration</a></p>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>