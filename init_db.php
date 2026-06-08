<?php

require 'config.php';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;charset=utf8",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 데이터베이스 생성
    $pdo->exec("CREATE DATABASE IF NOT EXISTS todo_app");
    
    // todo_app 데이터베이스로 변경
    $pdo->exec("USE todo_app");
    
    // users 테이블 생성
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // todos 테이블 생성
    $pdo->exec("CREATE TABLE IF NOT EXISTS todos (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        status ENUM('incomplete', 'complete') DEFAULT 'incomplete',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    echo "✓ 데이터베이스 생성 완료\n";
    echo "✓ users 테이블 생성 완료\n";
    echo "✓ todos 테이블 생성 완료\n";
    
} catch (PDOException $e) {
    die("DB 오류: " . $e->getMessage());
}

?>
