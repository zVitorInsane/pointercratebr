<?php
session_start();

// Ativa sessão com cookie, se necessário
if (!isset($_SESSION['username']) && isset($_COOKIE['username'], $_COOKIE['user_id'], $_COOKIE['is_admin'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['is_admin'] = (int) $_COOKIE['is_admin'];
}

// Verificação correta de admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] < 1) {
    die("Acesso negado.");
}

// Conexão segura com o banco de dados
$host = "node-sb2.blazebr.com:3306";
$user = "u7677_M84OY4UYie";
$pass = "WuQ8eh.+qIT=.YT+VQat4Zcu";
$db = "s7677_pointercreatebr";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validação e sanitização dos dados recebidos
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $level_name = filter_input(INPUT_POST, 'level_name', FILTER_SANITIZE_STRING);
    $level_id = filter_input(INPUT_POST, 'level_id', FILTER_SANITIZE_STRING);
    $hardest_rank = filter_input(INPUT_POST, 'hardest_rank', FILTER_VALIDATE_INT);
    $points = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT);

    if ($user_id && $level_name && $level_id && $hardest_rank !== false && $points !== false) {
        $stmt = $conn->prepare("INSERT INTO achievements (user_id, level_name, level_id, hardest_rank, points) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiii", $user_id, $level_name, $level_id, $hardest_rank, $points);

        if ($stmt->execute()) {
            echo "Conquista adicionada com sucesso! <a href='admin.php'>Voltar</a>";
        } else {
            echo "Erro: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Dados inválidos. Certifique-se de preencher todos os campos corretamente.";
    }
}
$conn->close();
?>
