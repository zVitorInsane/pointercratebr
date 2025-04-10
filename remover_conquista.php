<?php
session_start();

// Restaurar sessão com base nos cookies
if (!isset($_SESSION['username']) && isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['is_admin'] = $_COOKIE['is_admin'];
}

// Verificação de admin
if (!isset($_SESSION['user_id'])) {
    echo "Acesso negado.";
    exit();
}

$host = "node-sb2.blazebr.com:3306";
$user = "u7677_M84OY4UYie";
$pass = "WuQ8eh.+qIT=.YT+VQat4Zcu";
$db = "s7677_pointercreatebr";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Consulta se o usuário é admin
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT isadmin FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0 || $result->fetch_assoc()['isadmin'] != 1) {
    echo "Acesso negado.";
    exit();
}

// Verifica se o ID da conquista foi passado
if (!isset($_GET['id'])) {
    echo "ID da conquista não especificado.";
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM achievements WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin.php");
    exit();
} else {
    echo "Erro ao remover conquista.";
}
?>
