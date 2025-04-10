<?php 
session_start();

if (!isset($_SESSION['username']) && isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['is_admin'] = $_COOKIE['is_admin'];
}?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pointercrate Brasil</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .ranking-options {
      display: flex;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
      margin-top: 30px;
    }

    .ranking-card {
      background-color: #1e1e1e;
      border: 2px solid #00bcd4;
      border-radius: 16px;
      padding: 30px 40px;
      text-align: center;
      color: #00bcd4;
      font-size: 20px;
      font-weight: bold;
      text-decoration: none;
      box-shadow: 0 0 12px rgba(0, 188, 212, 0.2);
      transition: transform 0.2s, box-shadow 0.3s;
    }

    .ranking-card:hover {
      transform: scale(1.05);
      box-shadow: 0 0 20px rgba(0, 188, 212, 0.4);
    }

    .ranking-card .emoji {
      display: block;
      font-size: 40px;
      margin-bottom: 10px;
    }

  </style>
</head>
<body>

  <?php include 'nav.php'; ?>

  <main class="content">
    <h3 style="text-align:center;">Ranking Pointercrate Brasil</h3>
    <p style="text-align:center;">Acompanhe os melhores jogadores de Geometry Dash do Brasil.</p>

    <div class="ranking-options">
      <a href="ranking.php" class="ranking-card">
        <span class="emoji">📊</span>
        <span>Ranking Geral</span>
      </a>

      <a href="ranking_estado.php" class="ranking-card">
        <span class="emoji">📍</span>
        <span>Ranking por Estado</span>
      </a>
    </div>
  </main>

  <?php include 'footer.php'; ?>
</body>
</html>
