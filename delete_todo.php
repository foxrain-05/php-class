<?php

require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));

if ($id <= 0) {
    die("잘못된 요청입니다");
}

try {
    // WHERE id = ? AND user_id = ?
    // = 자신의 TODO만 삭제 가능
    $sql = "DELETE FROM todos WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $user_id]);

    header("Location: list_todos.php?page=" . $page);
    exit;

} catch (PDOException $e) {
    die("삭제 실패: " . $e->getMessage());
}

?>
