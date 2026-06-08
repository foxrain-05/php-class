<?php

require 'config.php';

try {
    // SELECT 쿼리
    // WHERE user_id = ? = 현재 로그인한 사용자의 TODO만 조회
    // ORDER BY created_at DESC = 최신순으로 정렬
    $sql = "SELECT id, title, status, created_at FROM todos 
            WHERE user_id = ? 
            ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
  
    // fetchAll() = 모든 결과를 배열로 가져옴
    // PDO::FETCH_ASSOC = 연관배열로 반환 (컬럼명이 키)
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
} catch (PDOException $e) {
    die("조회 실패: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>TODO 목록</title>
    <style>
        body { font-family: '맑은 고딕'; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: navy; }
        a { display: inline-block; margin: 10px 5px 10px 0; padding: 8px 12px; background: navy; color: white; text-decoration: none; }
        .todo-item { border: 1px solid #ddd; padding: 12px; margin: 10px 0; background: #f9f9f9; display: flex; justify-content: space-between; align-items: center; }
        .todo-title { font-weight: bold; color: navy; }
        .todo-status { font-size: 12px; color: #666; }
        .status-complete { color: green; }
        .empty { color: #999; padding: 20px; }
        .action a { padding: 4px 8px; margin-left: 5px; font-size: 12px; }
    </style>
</head>
<body>

<h1>📋 나의 TODO 목록</h1>

<a href="add_todo.php">➕ 새 TODO 추가</a>
<a href="logout.php">🚪 로그아웃</a>

<?php if (empty($todos)): ?>
    <div class="empty">해야 할 일이 없습니다.</div>
<?php else: ?>
    <?php foreach ($todos as $todo): ?>
        <div class="todo-item">
            <div>
                <div class="todo-title"><?php echo htmlspecialchars($todo['title']); ?></div>
                <div class="todo-status">
                    <?php echo $todo['created_at']; ?>
                    <span class="<?php echo $todo['status'] === 'complete' ? 'status-complete' : ''; ?>">
                        (<?php echo $todo['status'] === 'complete' ? '완료' : '미완료'; ?>)
                    </span>
                </div>
            </div>
            <div class="action">
                <a href="edit_todo.php?id=<?php echo $todo['id']; ?>">수정</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>