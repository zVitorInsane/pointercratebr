<?php
$host = "node-sb2.blazebr.com:3306";
$user = "u7677_M84OY4UYie";
$pass = "WuQ8eh.+qIT=.YT+VQat4Zcu";
$db = "s7677_pointercreatebr";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

$estados_brasil = [
    "AC" => "Acre", "AL" => "Alagoas", "AP" => "Amapá", "AM" => "Amazonas", "BA" => "Bahia",
    "CE" => "Ceará", "DF" => "Distrito Federal", "ES" => "Espírito Santo", "GO" => "Goiás",
    "MA" => "Maranhão", "MT" => "Mato Grosso", "MS" => "Mato Grosso do Sul", "MG" => "Minas Gerais",
    "PA" => "Pará", "PB" => "Paraíba", "PR" => "Paraná", "PE" => "Pernambuco", "PI" => "Piauí",
    "RJ" => "Rio de Janeiro", "RN" => "Rio Grande do Norte", "RS" => "Rio Grande do Sul",
    "RO" => "Rondônia", "RR" => "Roraima", "SC" => "Santa Catarina", "SP" => "São Paulo",
    "SE" => "Sergipe", "TO" => "Tocantins"
];

$estado_selecionado = isset($_GET['estado']) ? $_GET['estado'] : null;
$ranking_estado = null;

if ($estado_selecionado && array_key_exists($estado_selecionado, $estados_brasil)) {
    $stmt = $conn->prepare("
        SELECT u.id, u.username, u.state, u.profile_pic,
               a.level_name, a.hardest_rank, a.video
        FROM users u
        JOIN achievements a ON u.id = a.user_id
        JOIN (
            SELECT user_id, MIN(hardest_rank) as min_rank
            FROM achievements
            GROUP BY user_id
        ) m ON a.user_id = m.user_id AND a.hardest_rank = m.min_rank
        WHERE u.state = ?
        ORDER BY a.hardest_rank ASC
    ");
    $stmt->bind_param("s", $estado_selecionado);
    $stmt->execute();
    $ranking_estado = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Ranking por Estado - Pointercrate Brasil</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="ranking-style.css">
</head>
<body>
  <?php include 'nav.php'; ?>

  <div class="content">
    <h2>Ranking por Estado</h2>

    <form method="GET" action="" style="text-align: center; margin-bottom: 20px;">
      <div style="display: inline-flex; align-items: center; gap: 10px;">
        <select name="estado" id="estado" 
          style="padding: 8px 12px; background-color: #222; color: #00eaff; border: 1px solid #00eaff55; border-radius: 6px; font-weight: bold; outline: none;">
          <option value="">-- Escolha um estado --</option>
          <?php foreach ($estados_brasil as $sigla => $nome): ?>
            <option value="<?php echo $sigla; ?>" <?php if ($estado_selecionado === $sigla) echo 'selected'; ?>>
              <?php echo $nome; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" 
          style="background-color: #1a1a1a; color: #00eaff; padding: 8px 16px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; transition: background 0.2s, transform 0.2s; border: 1px solid #00eaff33;">
          🔍 Buscar
        </button>
      </div>
    </form>

    <?php if ($ranking_estado): ?>
      <h3>Ranking de <?php echo $estados_brasil[$estado_selecionado]; ?></h3>
      <ol>
        <?php $pos = 1; while ($p = $ranking_estado->fetch_assoc()): ?>
          <li>
            <img src="<?php echo htmlspecialchars($p['profile_pic']); ?>" alt="foto">
            <a href="perfil.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['username']); ?></a>
            — Top <?php echo $pos++; ?> — Hardest: 
            <strong>
              <?php if (!empty($p['video'])): ?>
                <a href="<?php echo htmlspecialchars($p['video']); ?>" target="_blank">
                  <?php echo htmlspecialchars($p['level_name']); ?>
                </a>
              <?php else: ?>
                <?php echo htmlspecialchars($p['level_name']); ?>
              <?php endif; ?>
            </strong> (pos <?php echo $p['hardest_rank']; ?>)
          </li>
        <?php endwhile; ?>
      </ol>
    <?php elseif ($estado_selecionado): ?>
      <p>Nenhum jogador encontrado para este estado.</p>
    <?php endif; ?>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
