<?php
$host = "node-sb2.blazebr.com:3306";
$user = "u7677_M84OY4UYie";
$pass = "WuQ8eh.+qIT=.YT+VQat4Zcu";
$db = "s7677_pointercreatebr";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

$ranking_pontos = $conn->query("
  SELECT u.id, u.username, u.state, u.profile_pic,
         SUM(a.points) as total_pontos
  FROM users u
  JOIN achievements a ON u.id = a.user_id
  GROUP BY u.id
  ORDER BY total_pontos DESC
");

$ranking_hardest = $conn->query("
  SELECT u.id, u.username, u.state, u.profile_pic, a.level_name, a.hardest_rank, a.video
  FROM users u
  JOIN achievements a ON u.id = a.user_id
  JOIN (
      SELECT user_id, MIN(hardest_rank) as min_rank
      FROM achievements
      GROUP BY user_id
  ) m ON a.user_id = m.user_id AND a.hardest_rank = m.min_rank
  ORDER BY a.hardest_rank ASC
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Ranking - Pointercrate Brasil</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #121212;
      color: #fff;
    }

    .content {
      max-width: 800px;
      margin: auto;
      padding: 20px;
    }

    .tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .tab-button {
      padding: 10px 15px;
      border: none;
      cursor: pointer;
      background-color: #333;
      color: #fff;
      border-radius: 5px;
    }

    .tab-button.active {
      background-color: #00bcd4;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    ol {
      list-style: none;
      padding: 0;
    }

    li {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 0;
      border-bottom: 1px solid #333;
      flex-wrap: wrap;
    }

    li img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }

    li a {
      color: #00bcd4;
      text-decoration: none;
      font-weight: bold;
      z-index: 1;
    }

    li a:hover {
      text-decoration: underline;
    }

    h3 {
      margin-top: 0;
      margin-bottom: 10px;
      color: #00bcd4;
    }
  </style>
</head>
<body>
  <?php include 'nav.php'; ?>

  <div class="content">
    <h2>Ranking Pointercrate Brasil</h2>

    <div class="tabs">
      <button class="tab-button active" onclick="openTab('pontos')">🏆 Top Pontuação</button>
      <button class="tab-button" onclick="openTab('hardest')">🔝 Top Hardest</button>
    </div>

    <div id="pontos" class="tab-content active">
      <h3>🏆 Ranking por Pontuação</h3>
      <ol>
        <?php $pos = 1; while ($p = $ranking_pontos->fetch_assoc()): ?>
          <li>
            <img src="<?php echo htmlspecialchars($p['profile_pic'] ?? 'default.png'); ?>" alt="foto">
            <a href="perfil.php?id=<?php echo $p['id']; ?>">
              <?php echo htmlspecialchars($p['username']); ?>
            </a>
            — Top <?php echo $pos++; ?> — Pontos:
            <strong><?php echo (int)$p['total_pontos']; ?></strong>
          </li>
        <?php endwhile; ?>
      </ol>
    </div>

    <div id="hardest" class="tab-content">
      <h3>🔝 Ranking por Hardest</h3>
      <ol>
        <?php $pos = 1; while ($h = $ranking_hardest->fetch_assoc()): ?>
          <li>
            <img src="<?php echo htmlspecialchars($h['profile_pic']); ?>" alt="foto">
            <a href="perfil.php?id=<?php echo $h['id']; ?>"><?php echo htmlspecialchars($h['username']); ?></a> 
            — Top <?php echo $pos++; ?> — Hardest:
            <strong>
              <?php if (!empty($h['video'])): ?>
                <a href="<?php echo htmlspecialchars($h['video']); ?>" target="_blank">
                  <?php echo htmlspecialchars($h['level_name']); ?>
                </a>
              <?php else: ?>
                <?php echo htmlspecialchars($h['level_name']); ?>
              <?php endif; ?>
            </strong> (pos <?php echo $h['hardest_rank']; ?>)
          </li>
        <?php endwhile; ?>
      </ol>
    </div>
  </div>

  <script>
    function openTab(tabName) {
      document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
      });

      document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
      });

      document.getElementById(tabName).classList.add('active');

      if (tabName === 'pontos') {
        document.querySelectorAll('.tab-button')[0].classList.add('active');
      } else {
        document.querySelectorAll('.tab-button')[1].classList.add('active');
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      openTab('pontos');
    });
  </script>

  <?php include 'footer.php'; ?>
</body>
</html>
