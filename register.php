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

$msg = "";

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $state = $_POST['state'];
    $profile_pic = $_POST['profile_pic'] ?: "https://i.imgur.com/xeN3J6Y.png";

    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $msg = "Nome de usuário já está em uso.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, state, profile_pic) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $state, $profile_pic);
        if ($stmt->execute()) {
            $msg = "Registrado com sucesso! Você pode fazer login.";
        } else {
            $msg = "Erro ao registrar.";
        }
        $stmt->close();
    }

    $check->close();
}

$estados = [
    'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA',
    'MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN',
    'RS','RO','RR','SC','SP','SE','TO'
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Registrar - Pointercrate Brasil</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'nav.php'; ?>

  <main class="content">
    <h3>Criar Conta</h3>

    <?php if ($msg): ?>
      <p><?php echo $msg; ?></p>
    <?php endif; ?>

    <form method="POST">
      <label>Nome de Usuário:</label>
      <input type="text" name="username" required>

      <label>Senha:</label>
      <input type="password" name="password" required>

      <label>Estado:</label>
      <select name="state" required>
        <option value="">-- Selecione seu estado --</option>
        <?php foreach ($estados as $uf): ?>
          <option value="<?php echo $uf; ?>"><?php echo $uf; ?></option>
        <?php endforeach; ?>
      </select>

      <label>URL da Foto de Perfil (opcional):</label>
      <input type="url" name="profile_pic" placeholder="https://...">

      <input type="submit" name="submit" value="Registrar">
    </form>
  </main>

  <footer>
    &copy; 2025 Pointercrate Brasil - Feito por zVitorInsane
  </footer>
</body>
</html>
