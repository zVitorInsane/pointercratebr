<?php
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['username']) && isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['is_admin'] = $_COOKIE['is_admin'];
}

// Verifica se a URL da imagem é válida e existe
function is_valid_image_url($url) {
    $headers = @get_headers($url, 1);
    return $headers && strpos($headers[0], '200') !== false && isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'image/') === 0;
}

$profile_pic = "imagens/default.jpg"; // Imagem padrão

// Conecta e busca do banco
if (isset($_SESSION['user_id'])) {
    $conn = new mysqli("node-sb2.blazebr.com:3306", "u7677_M84OY4UYie", "WuQ8eh.+qIT=.YT+VQat4Zcu", "s7677_pointercreatebr");
    if (!$conn->connect_error) {
        $id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $url = trim($row['profile_pic']);
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL) && is_valid_image_url($url)) {
                $profile_pic = $url;
            }
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<style>
  nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #111;
    padding: 10px 20px;
    border-bottom: 2px solid #00bcd4;
    position: relative;
  }

  .nav-links {
    display: flex;
    gap: 20px;
    align-items: center;
  }

  .nav-links a {
    color: white;
    text-decoration: none;
    font-weight: bold;
  }

  .user-info-container {
    position: relative;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
  }

  .user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #00bcd4;
  }

  .user-info span {
    color: #00bcd4;
    font-weight: bold;
  }

  .user-dropdown {
    position: absolute;
    top: 45px;
    right: 0;
    background-color: #1e1e1e;
    border: 1px solid #00bcd4;
    border-radius: 8px;
    display: none;
    flex-direction: column;
    min-width: 130px;
    z-index: 999;
  }

  .user-dropdown a {
    padding: 10px;
    color: #00bcd4;
    text-decoration: none;
  }

  .user-dropdown a:hover {
    background-color: #111;
  }

  .user-dropdown.show {
    display: flex;
  }
</style>

<header>
    <h2>Pointercrate Brasil</h2>
</header>

<nav>
  <div class="nav-links">
    <a href="index.php">Início</a>
    <a href="ranking.php">Ranking</a>
    <a href="ranking_estado.php">Ranking por Estado</a>
  </div>

  <?php if (isset($_SESSION['username'])): ?>
    <div class="user-info-container" id="userMenu">
      <div class="user-info" onclick="toggleDropdown()">
        <img class="user-avatar" src="<?= htmlspecialchars($profile_pic) ?>" alt="Avatar">
        <span><?= htmlspecialchars($_SESSION['username']) ?></span>
      </div>
      <div class="user-dropdown" id="userDropdown"
           onmouseover="keepDropdownOpen()"
           onmouseleave="hideDropdown()">
        <a href="perfil.php?id=<?= $_SESSION['user_id'] ?>">👤 Perfil</a>
        <a href="logout.php">📜 Logout</a>
      </div>
    </div>
  <?php else: ?>
    <div class="nav-links">
      <a href="login.php">Login</a>
      <a href="register.php">Registrar</a>
    </div>
  <?php endif; ?>
</nav>

<script>
  let dropdown = document.getElementById("userDropdown");
  let keepOpen = false;

  function toggleDropdown() {
    dropdown.classList.toggle("show");
  }

  function keepDropdownOpen() {
    keepOpen = true;
  }

  function hideDropdown() {
    keepOpen = false;
    setTimeout(() => {
      if (!keepOpen) dropdown.classList.remove("show");
    }, 200);
  }

  // Fecha se clicar fora
  document.addEventListener("click", function (event) {
    const isClickInside = document.getElementById("userMenu").contains(event.target);
    if (!isClickInside) {
      dropdown.classList.remove("show");
    }
  });
</script>
