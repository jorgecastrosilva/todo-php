<?php
session_start();
require __DIR__ . '/lib.php';

// Ultra-simple : tableau d'utilisateurs en clair (change-les !)
$USERS = [
    'admin' => 'secret',   // utilisateur: admin / mot de passe: secret
    // 'jorge' => 'monmdp', // exemple
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    if (isset($USERS[$u]) && hash_equals($USERS[$u], $p)) {
        $_SESSION['user'] = $u;
        log_event('login_success', $u);
        header('Location: todo.php');
        exit;
    } else {
        log_event('login_failed', $u);
        $error = "Identifiants invalides.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Login — To-Do</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f6f7fb;margin:0;padding:0}
  .wrap{max-width:360px;margin:10vh auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 6px 20px rgba(0,0,0,.06)}
  h1{font-size:20px;margin:0 0 12px}
  label{display:block;margin:10px 0 6px;font-size:14px}
  input[type=text],input[type=password]{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px}
  button{width:100%;padding:10px;border:0;border-radius:10px;background:#111827;color:#fff;font-weight:600;margin-top:12px;cursor:pointer}
  .error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:10px;font-size:14px}
  .hint{margin-top:12px;color:#6b7280;font-size:12px}
</style>
</head>
<body>
<div class="wrap">
  <h1>Connexion</h1>
  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <label for="username">Utilisateur</label>
    <input id="username" name="username" type="text" required autofocus>

    <label for="password">Mot de passe</label>
    <input id="password" name="password" type="password" required>

    <button type="submit">Se connecter</button>
  </form>
  <p class="hint">Démo : <b>admin</b> / <b>secret</b> (modifie dans <code>login.php</code>)</p>
</div>
</body>
</html>
