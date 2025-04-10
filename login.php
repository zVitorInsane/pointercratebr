<?php
session_start();

if (!isset($_SESSION['username']) && isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['is_admin'] = $_COOKIE['is_admin'];
}

$host = "node-sb2.blazebr.com:3306";
$user = "u7677_M84OY4UYie";
$pass = "WuQ8eh.+qIT=.YT+VQat4Zcu";
$db = "s7677_pointercreatebr";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$erro = "";

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];

            // Salvar os cookies por 30 dias
            setcookie("username", $user['username'], time() + (86400 * 30), "/");
            setcookie("user_id", $user['id'], time() + (86400 * 30), "/");
            setcookie("is_admin", $user['is_admin'], time() + (86400 * 30), "/");

            if ($user['is_admin'] == 1) {
                header("Location: admin.php");
            } else {
                header("Location: perfil.php?id=" . $user['id']);
            }
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - Pointercrate Brasil</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include 'nav.php'; ?>

  <main class="content">
    <h3>Login</h3>

    <?php if (isset($_GET['logout'])): ?>
      <p class="mensagem">Logout efetuado com sucesso.</p>
    <?php endif; ?>

    <?php if (!empty($erro)): ?>
      <p class="erro"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="post" action="">
      <label>Usuário:</label>
      <input type="text" name="username" placeholder="Usuário" required>

      <label>Senha:</label>
      <input type="password" name="password" placeholder="Senha" required>

      <input type="submit" name="submit" value="Entrar">
    </form>

    <p style="text-align: center; margin-top: 10px;">
      <a href="register.php">Criar conta</a>
    </p>
  </main>

  <?php include 'footer.php'; ?>
</body>
</html>
