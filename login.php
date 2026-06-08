<?php

require 'config.php';
session_start();

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

// 이미 로그인했으면 목록으로 이동
if (isset($_SESSION['user_id'])) {
    header("Location: list_todos.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username_input = htmlspecialchars($_POST['username'] ?? '');
        $password_input = htmlspecialchars($_POST['password'] ?? '');
  
        // 입력값 검증
        if (empty($username_input) || empty($password_input)) {
            throw new Exception("사용자명과 비밀번호를 입력하세요");
        }
  
        // 데이터베이스에서 사용자 조회
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
        // password_verify(): 입력한 비밀번호와 bcrypt 해시 비교
        if ($user && password_verify($password_input, $user['password'])) {
            // 로그인 성공
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: list_todos.php");
            exit;
        } else {
            throw new Exception("사용자명 또는 비밀번호가 잘못되었습니다");
        }
  
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>로그인 - TODO 관리 시스템</title>
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
  
        .login-container {
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
  
        button {
            width: 100%;
            padding: 12px;
            background-color: navy;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
  
        button:hover {
            background-color: #000080;
        }

        .signup-link {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background-color: #4caf50;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
        }

        .signup-link:hover {
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
  
        .test-info {
            background-color: #e8f5e9;
            padding: 12px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #4caf50;
            font-size: 12px;
            color: #333;
        }
  
        .test-info strong {
            display: block;
            margin-bottom: 5px;
            color: #2e7d32;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>📋 TODO 관리 시스템</h1>
  
    <?php if ($error): ?>
        <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
  
    <form method="POST">
        <div class="form-group">
            <label for="username">사용자명:</label>
            <input type="text" id="username" name="username" placeholder="사용자명 입력" required autofocus>
        </div>
  
        <div class="form-group">
            <label for="password">비밀번호:</label>
            <input type="password" id="password" name="password" placeholder="비밀번호 입력" required>
        </div>
  
        <button type="submit">로그인하기</button>
    </form>

    <a class="signup-link" href="register.php">회원가입</a>
  
    <div class="test-info">
        <strong>📝 테스트 계정</strong>
        사용자명: john<br>
        비밀번호: password123<br>
        <br>
        <small>비밀번호는 bcrypt로 안전하게 암호화됩니다</small>
    </div>
</div>

</body>
</html>