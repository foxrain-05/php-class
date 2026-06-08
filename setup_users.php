<?php

$host = 'localhost';
$dbname = 'todo_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
    // 사용자 데이터
    $users = array(
        array(
            'username' => 'john',
            'plain_password' => 'password123',
            'display_name' => 'John (비밀번호: password123)'
        ),
        array(
            'username' => 'admin',
            'plain_password' => 'admin123',
            'display_name' => 'Admin (비밀번호: admin123)'
        )
    );
  
    // 사용자 삽입
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
  
    foreach ($users as $user) {
        // password_hash(): 비밀번호를 bcrypt로 안전하게 해싱
        $hashed = password_hash($user['plain_password'], PASSWORD_BCRYPT);
  
        $stmt->execute(array(
            $user['username'],
            $hashed
        ));
  
        echo "✅ " . $user['display_name'] . " 사용자 생성 완료<br>";
    }
  
    // TODO 샘플 데이터
    // 첫 번째 사용자(john, id=1)의 TODO 추가
    $todo_sql = "INSERT INTO todos (user_id, title, status) VALUES (?, ?, ?)";
    $todo_stmt = $pdo->prepare($todo_sql);
  
    $todos = array(
        array('user_id' => 1, 'title' => '장보기', 'status' => 'incomplete'),
        array('user_id' => 1, 'title' => '공부하기', 'status' => 'complete')
    );
  
    foreach ($todos as $todo) {
        $todo_stmt->execute(array(
            $todo['user_id'],
            $todo['title'],
            $todo['status']
        ));
    }
  
    echo "<br>✅ 모든 데이터가 생성되었습니다!<br><br>";
    echo "📝 로그인 테스트:<br>";
    echo "사용자명: john<br>";
    echo "비밀번호: password123<br>";
  
} catch (PDOException $e) {
    echo "❌ 오류: " . $e->getMessage();
}

?>