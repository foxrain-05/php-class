<?php

require 'config.php';

$error = '';
$todo = null;
$id = $_GET['id'] ?? null;

if (!$id) {
    die("잘못된 요청입니다");
}

// 기존 TODO 데이터 조회
try {
    // WHERE id = ? AND user_id = ?
    // = 자신의 TODO인지 확인 (보안)
    $sql = "SELECT id, title, status FROM todos WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $user_id]);
  
    // fetch() = 하나의 행(row)을 가져옴
    $todo = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if (!$todo) {
        die("TODO를 찾을 수 없습니다");
    }
  
} catch (PDOException $e) {
    die("조회 실패: " . $e->getMessage());
}

// POST 요청 처리 (수정)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = htmlspecialchars(trim($_POST['title'] ?? ''));
  
        if (empty($title)) {
            throw new Exception("제목을 입력하세요");
        }
  
        // UPDATE 쿼리
        // WHERE id = ? AND user_id = ?
        // = 자신의 TODO만 수정 가능
        $sql = "UPDATE todos SET title = ? WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $id, $user_id]);
  
        // 수정 완료 후 목록으로 이동
        header("Location: list_todos.php");
        exit;
  
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>TODO 수정</title>
    <style>
        body { font-family: '맑은 고딕'; max-width: 600px; margin: 50px auto; padding: 20px; }
        h1 { color: navy; }
        form { background: #f5f5f5; padding: 15px; }
        input { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ddd; }
        button { background: navy; color: white; padding: 8px 15px; border: none; cursor: pointer; }
        .error { color: red; padding: 8px; background: #ffe6e6; }
        a { color: navy; text-decoration: none; }
    </style>
</head>
<body>
d
<h1>✏️ TODO 수정</h1>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($todo['title']); ?>" required autofocus>
    <button type="submit">수정하기</button>
</form>

<a href="list_todos.php">목록으로 돌아가기</a>

</body>
</html>