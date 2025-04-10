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

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Usuário não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil</title>
  <link rel="stylesheet" href="style.css">
  <style>
    button:hover {
      background-color: #45a049;
    }
  </style>

</head>
<body>

<h2>Editar Perfil</h2>
  <form action="salvar_edicao.php" method="POST">
    <label>Nome completo:</label><br>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required><br><br>

    <label>Gênero:</label><br>
    <select name="gender" required>
      <option value="Masculino" <?php if ($user['gender'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
      <option value="Feminino" <?php if ($user['gender'] == 'Feminino') echo 'selected'; ?>>Feminino</option>
      <option value="Outro" <?php if ($user['gender'] == 'Outro') echo 'selected'; ?>>Outro</option>
    </select><br><br>

    <label>Estado:</label><br>
    <input type="text" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required><br><br>

    <label>Biografia:</label><br>
    <textarea name="bio" rows="5" cols="40"><?php echo htmlspecialchars($user['bio']); ?></textarea><br><br>

    <label>Link da nova foto de perfil:</label><br>
    <input type="url" name="profile_pic_url" placeholder="https://exemplo.com/imagem.png"
           value="<?php echo htmlspecialchars($user['profile_pic']); ?>" style="width: 300px;"><br><br>

    <button type="submit" style="
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    ">
      💾 Salvar alterações
    </button>

  </form>

  <p><a href="perfil.php?id=<?php echo $id; ?>">← Voltar ao perfil</a></p>
</body>
</html>
