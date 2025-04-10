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

if (!isset($_GET['id'])) {
    echo "ID do jogador não especificado.";
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Jogador não encontrado.";
    exit();
}

$player = $result->fetch_assoc();

// Buscar conquistas
$stmt2 = $conn->prepare("
    SELECT level_name, hardest_rank, points, level_id, video
    FROM achievements
    WHERE user_id = ?
    ORDER BY points DESC
");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$conquistas = $stmt2->get_result();

$total_pontos = 0;
$hardest = null;
$hardest_nome = null;
$hardest_video = null;
$conquistas_array = [];

while ($row = $conquistas->fetch_assoc()) {
    $total_pontos += $row['points'];
    if ($hardest === null || $row['hardest_rank'] < $hardest) {
        $hardest = $row['hardest_rank'];
        $hardest_nome = $row['level_name'];
        $hardest_video = $row['video'];
    }
    $conquistas_array[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Perfil de <?php echo htmlspecialchars($player['username']); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'nav.php'; ?>

  <div class="content">
    <h2>Perfil de <?php echo htmlspecialchars($player['username']); ?></h2>

    <?php if (!empty($player['profile_pic'])): ?>
      <img src="<?php echo $player['profile_pic']; ?>" alt="Foto de perfil" width="150" style="border-radius: 12px;">
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $player['id']): ?>
      <p><a href="editar_perfil.php">✏️ Editar Perfil</a></p>
    <?php endif; ?>

    <p><strong>Nome completo:</strong> <?php echo htmlspecialchars($player['full_name']); ?></p>
    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($player['gender']); ?></p>
    <p><strong>Estado:</strong> <?php echo htmlspecialchars($player['state']); ?></p>
    <p><strong>Biografia:</strong><br> <?php echo nl2br(htmlspecialchars($player['bio'])); ?></p>

    <hr>

    <p><strong>Pontuação total:</strong> <?php echo $total_pontos; ?> pts</p>
    <p><strong>Hardest atual:</strong>
      <?php
        if ($hardest !== null) {
            echo "<a href=\"" . htmlspecialchars($hardest_video) . "\" target=\"_blank\">#" . $hardest . " - " . htmlspecialchars($hardest_nome) . "</a>";
        } else {
            echo "Nenhum nível registrado";
        }
      ?>
    </p>

    <?php if (count($conquistas_array) > 0): ?>
      <h3>🎯 Conquistas</h3>
      <table style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th style="text-align: left; padding: 8px;">Level</th>
            <th style="text-align: center;">Hardest</th>
            <th style="text-align: center;">Pontos</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($conquistas_array as $c): ?>
            <tr style="border-top: 1px solid #333;">
              <td style="padding: 8px;">
                <a href="<?php echo htmlspecialchars($c['video']); ?>" target="_blank">
                  <?php echo htmlspecialchars($c['level_name']); ?>
                </a>
              </td>
              <td style="text-align: center;">#<?php echo $c['hardest_rank']; ?></td>
              <td style="text-align: center;"><?php echo $c['points']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Esse jogador ainda não possui conquistas registradas.</p>
    <?php endif; ?>

    <p><a href="ranking.php">← Voltar ao ranking</a></p>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
