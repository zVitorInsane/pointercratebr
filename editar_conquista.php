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
    echo "Acesso negado.";
    exit();
}

// Conexão com o banco
$host = "node-sb2.blazebr.com:3306";
$user = "u7677_M84OY4UYie";
$pass = "WuQ8eh.+qIT=.YT+VQat4Zcu";
$db = "s7677_pointercreatebr";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verifica se ID foi fornecido
if (!isset($_GET['id'])) {
    echo "ID da conquista não fornecido.";
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação e sanitização dos dados
    $level_name = filter_input(INPUT_POST, 'level_name', FILTER_SANITIZE_STRING);
    $hardest_rank = filter_input(INPUT_POST, 'hardest_rank', FILTER_VALIDATE_INT);
    $points = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT);

    if ($level_name && $hardest_rank !== false && $points !== false) {
        $stmt = $conn->prepare("UPDATE achievements SET level_name = ?, hardest_rank = ?, points = ? WHERE id = ?");
        $stmt->bind_param("siii", $level_name, $hardest_rank, $points, $id);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Erro ao atualizar a conquista.";
        }
    } else {
        echo "Dados inválidos.";
    }
}

// Buscar os dados atuais da conquista
$stmt = $conn->prepare("SELECT * FROM achievements WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Conquista não encontrada.";
    exit();
}

$conquista = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Conquista</title>
</head>
<body>
  <h2>Editar Conquista</h2>
  <form method="POST">
    <label>Nível:</label><br>
    <input type="text" name="level_name" value="<?php echo htmlspecialchars($conquista['level_name']); ?>" required><br>

    <label>Ranking (posição no hardest):</label><br>
    <input type="number" name="hardest_rank" value="<?php echo $conquista['hardest_rank']; ?>" required><br>

    <label>Pontos:</label><br>
    <input type="number" name="points" value="<?php echo $conquista['points']; ?>" required><br><br>

    <input type="submit" value="Salvar alterações">
  </form>

  <p><a href="admin.php">← Voltar para o painel</a></p>
</body>
</html>
