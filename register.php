<?php

// 데이터베이스 연결
$host = 'localhost';
$dbname = 'todo_app';
$username = 'root';
<<<<<<< ours
$password = '';
=======
$password = '1234';
>>>>>>> theirs

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB 연결 실패: " . $e->getMessage());
}

// 세션 시작
session_start();

// 이미 로그인했으면 목록으로 이동
if (isset($_SESSION['user_id'])) {
    header("Location: list_todos.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username_input = trim($_POST['username'] ?? '');
        $password_input = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // 입력값 검증
        if (empty($username_input) || empty($password_input) || empty($password_confirm)) {
            throw new Exception("사용자명과 비밀번호를 모두 입력하세요");
        }

        if (strlen($username_input) > 50) {
            throw new Exception("사용자명은 50자 이하여야 합니다");
        }

        if ($password_input !== $password_confirm) {
            throw new Exception("비밀번호 확인이 일치하지 않습니다");
        }

        // users.username은 UNIQUE이므로 중복 사용자명을 먼저 확인
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$username_input]);

        if ($check_stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("이미 사용 중인 사용자명입니다");
        }

        // password_hash(): 비밀번호를 bcrypt로 안전하게 해싱
        $hashed_password = password_hash($password_input, PASSWORD_BCRYPT);

        $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $insert_stmt = $pdo->prepare($insert_sql);
        $insert_stmt->execute([$username_input, $hashed_password]);

        $success = "회원가입이 완료되었습니다. 로그인해 주세요.";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>회원가입 - TODO 관리 시스템</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: '맑은 고딕', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            color: navy;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: navy;
            box-shadow: 0 0 5px rgba(0, 0, 139, 0.3);
        }

        button, .login-link {
            display: block;
            width: 100%;
            padding: 12px;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background-color: navy;
        }

        button:hover {
            background-color: #000080;
        }

        .login-link {
            margin-top: 10px;
            background-color: #4caf50;
        }

        .login-link:hover {
            background-color: #388e3c;
        }

        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #d32f2f;
        }

        .success {
            color: #2e7d32;
            background-color: #e8f5e9;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }
    </style>
</head>
<body>

<div class="signup-container">
    <h1>📝 회원가입</h1>

    <?php if ($error): ?>
        <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">✅ <?php echo htmlspecialchars($success); ?></div>
        <a class="login-link" href="login.php">로그인하러 가기</a>
    <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">사용자명:</label>
                <input type="text" id="username" name="username" maxlength="50" placeholder="사용자명 입력" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">비밀번호:</label>
                <input type="password" id="password" name="password" placeholder="비밀번호 입력" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">비밀번호 확인:</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="비밀번호 다시 입력" required>
            </div>

            <button type="submit">가입하기</button>
        </form>

        <a class="login-link" href="login.php">로그인으로 돌아가기</a>
    <?php endif; ?>
</div>

</body>
</html>
