<?php

require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB 연결 실패: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list_todos.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$page = max(1, (int)($_POST['page'] ?? 1));

if ($id <= 0) {
    die("잘못된 요청입니다");
}

try {
    // 현재 TODO 상태 조회
    // WHERE id = ? AND user_id = ? = 자신의 TODO인지 확인
    $select_sql = "SELECT status FROM todos WHERE id = ? AND user_id = ?";
    $select_stmt = $pdo->prepare($select_sql);
    $select_stmt->execute([$id, $user_id]);
    $todo = $select_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$todo) {
        die("TODO를 찾을 수 없습니다");
    }

    // 현재 상태의 반대 상태로 변경
    $new_status = $todo['status'] === 'complete' ? 'incomplete' : 'complete';

    $update_sql = "UPDATE todos SET status = ? WHERE id = ? AND user_id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$new_status, $id, $user_id]);

    header("Location: list_todos.php?page=" . $page);
    exit;

} catch (PDOException $e) {
    die("상태 변경 실패: " . $e->getMessage());
}

?>
