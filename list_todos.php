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

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 5;
$offset = ($page - 1) * $per_page;
$total_pages = 1;

try {
    // 전체 TODO 개수 조회
    $count_sql = "SELECT COUNT(*) FROM todos WHERE user_id = ?";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute([$user_id]);
    $total_todos = (int)$count_stmt->fetchColumn();
    $total_pages = max(1, (int)ceil($total_todos / $per_page));

    if ($page > $total_pages) {
        header("Location: list_todos.php?page=" . $total_pages);
        exit;
    }

    // SELECT 쿼리
    // WHERE user_id = ? = 현재 로그인한 사용자의 TODO만 조회
    // ORDER BY created_at DESC = 최신순으로 정렬
    // LIMIT, OFFSET = 한 페이지에 5개씩 표시
    $sql = "SELECT id, title, status, created_at FROM todos 
            WHERE user_id = ? 
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $per_page, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();

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
        a, button { display: inline-block; margin: 10px 5px 10px 0; padding: 8px 12px; background: navy; color: white; text-decoration: none; border: none; cursor: pointer; font-family: inherit; }
        .todo-item { border: 1px solid #ddd; padding: 12px; margin: 10px 0; background: #f9f9f9; display: flex; justify-content: space-between; align-items: center; }
        .todo-title { font-weight: bold; color: navy; }
        .todo-status { font-size: 12px; color: #666; }
        .status-complete { color: green; }
        .empty { color: #999; padding: 20px; }
        .action { display: flex; gap: 5px; align-items: center; }
        .action a, .action button { padding: 4px 8px; margin: 0; font-size: 12px; }
        .action form { display: inline; margin: 0; }
        .delete-button { background: #d32f2f; }
        .toggle-button { background: #2e7d32; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination span { display: inline-block; margin: 10px 5px; padding: 8px 12px; color: #666; }
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
                <form method="POST" action="toggle_todo.php">
                    <input type="hidden" name="id" value="<?php echo $todo['id']; ?>">
                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                    <button type="submit" class="toggle-button">
                        <?php echo $todo['status'] === 'complete' ? '미완료로 변경' : '완료로 변경'; ?>
                    </button>
                </form>
                <a href="edit_todo.php?id=<?php echo $todo['id']; ?>">수정</a>
                <a class="delete-button" href="delete_todo.php?id=<?php echo $todo['id']; ?>&page=<?php echo $page; ?>" onclick="return confirm('정말 삭제하시겠습니까?');">삭제</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="list_todos.php?page=<?php echo $page - 1; ?>">이전</a>
    <?php else: ?>
        <span>이전</span>
    <?php endif; ?>

    <span><?php echo $page; ?> / <?php echo $total_pages; ?></span>

    <?php if ($page < $total_pages): ?>
        <a href="list_todos.php?page=<?php echo $page + 1; ?>">다음</a>
    <?php else: ?>
        <span>다음</span>
    <?php endif; ?>
</div>

</body>
</html>
