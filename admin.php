<?php
session_start();

// Ativa sessão com cookie, se necessário
if (!isset($_SESSION['username']) && isset($_COOKIE['username'], $_COOKIE['user_id'], $_COOKIE['is_admin'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['is_admin'] = (int) $_COOKIE['is_admin'];
}

// Verifica se o usuário está logado e tem nível admin 1 ou superior
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] < 1) {
    header("Location: index.php");
    exit();
}

// Conexão segura com o banco de dados
$host = "node-sb2.blazebr.com:3306";
$user = "u7677_M84OY4UYie";
$pass = "WuQ8eh.+qIT=.YT+VQat4Zcu";
$db = "s7677_pointercreatebr";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Busca todos os usuários
$users = $conn->query("SELECT id, username FROM users");

// Mensagem de sucesso ou erro
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Prevenção contra SQL Injection + sanitização de entrada
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $level_id = filter_input(INPUT_POST, 'level_id', FILTER_SANITIZE_STRING);
    $level_name = filter_input(INPUT_POST, 'level_name', FILTER_SANITIZE_STRING);
    $ranking = filter_input(INPUT_POST, 'ranking', FILTER_VALIDATE_INT);
    $points = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT);

    if ($user_id && $level_id && $level_name && $ranking !== false && $points !== false) {
        $stmt = $conn->prepare("INSERT INTO achievements (user_id, level_id, level_name, hardest_rank, points) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issii", $user_id, $level_id, $level_name, $ranking, $points);

        if ($stmt->execute()) {
            $msg = "Conquista adicionada com sucesso!";
        } else {
            $msg = "Erro ao adicionar conquista.";
        }
        $stmt->close();
    } else {
        $msg = "Dados inválidos. Por favor, preencha corretamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel Administrativo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include 'nav.php'; ?>

  <main class="content admin-panel">
    <h2>Painel Administrativo</h2>
    <p>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>

    <h3>Adicionar Conquista</h3>

    <?php if ($msg): ?>
      <p><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST">
      <label for="user_id">Jogador:</label>
      <select name="user_id" id="user_id" required>
        <?php while ($row = $users->fetch_assoc()): ?>
          <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></option>
        <?php endwhile; ?>
      </select>

      <label for="level_id">ID do nível na aredl.net:</label>
      <input type="text" name="level_id" id="level_id" required>

      <label for="level_name">Nome do nível:</label>
      <input type="text" name="level_name" id="level_name" required>

      <label for="ranking">Ranking (posição no hardest):</label>
      <input type="number" name="ranking" id="ranking" required>

      <label for="points">Pontuação:</label>
      <input type="number" name="points" id="points" required>

      <input type="submit" name="submit" value="Adicionar Conquista">
    </form>
  </main>

  <footer>
    &copy; 2025 Pointercrate Brasil - Feito por zVitorInsane
  </footer>
</body>
</html>
