<?php

require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = htmlspecialchars(trim($_POST['title'] ?? ''));
  
        // 검증
        if (empty($title)) {
            throw new Exception("제목을 입력하세요");
        }
        if (strlen($title) > 200) {
            throw new Exception("제목은 200자 이하여야 합니다");
        }
  
        // INSERT 쿼리 실행
        // prepare() = SQL 쿼리를 미리 준비 (? 자리에 나중에 값 입력)
        // execute() = 준비된 쿼리에 값을 넣어서 실행
        // 이 방식은 SQL 인젝션 공격으로부터 안전
        $sql = "INSERT INTO todos (user_id, title, status) VALUES (?, ?, 'incomplete')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $title]);
  
        $success = "TODO가 추가되었습니다!";
  
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>TODO 추가</title>
    <style>
        body { font-family: '맑은 고딕'; max-width: 600px; margin: 50px auto; padding: 20px; }
        h1 { color: navy; }
        form { background: #f5f5f5; padding: 15px; }
        input { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ddd; }
        button { background: navy; color: white; padding: 8px 15px; border: none; cursor: pointer; }
        .error { color: red; padding: 8px; background: #ffe6e6; }
        .success { color: green; padding: 8px; background: #e6ffe6; }
        a { color: navy; text-decoration: none; }
    </style>
</head>
<body>

<h1>📝 새 TODO 추가</h1>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <a href="list_todos.php">목록으로 돌아가기</a>
<?php else: ?>
    <form method="POST">
        <input type="text" name="title" placeholder="해야 할 일을 입력하세요" autofocus required>
        <button type="submit">추가하기</button>
    </form>
    <a href="list_todos.php">목록으로 돌아가기</a>
<?php endif; ?>

</body>
</html>