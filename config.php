<?php

// config.php - 모든 페이지에 포함

// 데이터베이스 연결
$host = 'localhost';
$dbname = 'todo_app';
$username = 'root';
$password = '1234';

try {
    // PDO = PHP Data Objects
    // = PHP에서 데이터베이스를 안전하게 다룰 수 있게 해주는 클래스
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
  
    // setAttribute()
    // = PDO의 동작 방식 설정
    // PDO::ATTR_ERRMODE = 에러 처리 방식
    // PDO::ERRMODE_EXCEPTION = 에러 발생 시 Exception 발생
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
} catch (PDOException $e) {
    // PDOException = 데이터베이스 관련 예외
    die("DB 연결 실패: " . $e->getMessage());
}

// 세션 시작
session_start();

// 로그인 확인
// isset() = 변수가 존재하는가?
// 로그인하지 않으면 login.php로 이동
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

?>