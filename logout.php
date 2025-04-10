<?php
session_start();
session_destroy();

setcookie("username", "", time() - 3600, "/");
setcookie("user_id", "", time() - 3600, "/");
setcookie("is_admin", "", time() - 3600, "/");

header("Location: login.php?logout=true");
exit();
?>