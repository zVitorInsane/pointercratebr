<?php
session_start();

// Restaurar sessão a partir dos cookies, se necessário
if (!isset($_SESSION['username']) && isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['is_admin'] = $_COOKIE['is_admin'];
}

// Verificação de login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

$id = intval($_SESSION['user_id']);

// Coletar dados do POST com segurança
$full_name = trim($_POST['full_name']);
$gender = trim($_POST['gender']);
$state = trim($_POST['state']);
$bio = trim($_POST['bio']);

$profile_pic_url = !empty($_POST['profile_pic_url']) && filter_var($_POST['profile_pic_url'], FILTER_VALIDATE_URL)
    ? $_POST['profile_pic_url']
    : null;

// Atualiza com ou sem imagem de perfil
if ($profile_pic_url) {
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, gender = ?, state = ?, bio = ?, profile_pic = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $full_name, $gender, $state, $bio, $profile_pic_url, $id);
} else {
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, gender = ?, state = ?, bio = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $gender, $state, $bio, $id);
}

// Executar e redirecionar
if ($stmt->execute()) {
    header("Location: perfil.php?id=" . $id);
    exit();
} else {
    echo "Erro ao salvar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
