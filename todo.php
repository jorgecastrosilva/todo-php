<?php
require __DIR__ . '/lib.php';
require_login();

$user  = $_SESSION['user'];
$tasks = load_tasks($user);

// Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        if ($title !== '') {
            $task = [
                'id'      => uniqid('t_', true),
                'title'   => $title,
                'done'    => false,
                'created' => date('c')
            ];
            $tasks[] = $task;
            save_tasks($user, $tasks);
            log_event('task_add', $user, ['id' => $task['id'], 'title' => $title]);
        }
        header('Location: todo.php'); exit;
    }

    if ($action === 'toggle') {
        $id = $_POST['id'] ?? '';
        foreach ($tasks as &$t) {
            if ($t['id'] === $id) {
                $t['done'] = !$t['done'];
                save_tasks($user, $tasks);
                log_event('task_toggle', $user, ['id' => $id, 'done' => $t['done']]);
                break;
            }
        }
        header('Location: todo.php'); exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $before = count($tasks);
        $tasks = array_values(array_filter($tasks, fn($t) => $t['id'] !== $id));
        if (count($tasks) !== $before) {
            save_tasks($user, $tasks);
            log_event('task_delete', $user, ['id' => $id]);
        }
        header('Location: todo.php'); exit;
    }

    if ($action === 'clear') {
        $tasks = [];
        save_tasks($user, $tasks);
        log_event('task_clear', $user);
        header('Location: todo.php'); exit;
    }
}

$openCount  = count(array_filter($tasks, fn($t) => !$t['done']));
$totalCount = count($tasks);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>To-Do — <?= htmlspecialchars($user) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f6f7fb;margin:0}
  header,main{max-width:720px;margin:0 auto}
  header{display:flex;align-items:center;justify-content:space-between;padding:16px}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,.06);padding:16px;margin:16px}
  h1{font-size:20px;margin:0}
  .muted{color:#6b7280;font-size:14px}
  form.inline{display:inline}
  input[type=text]{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px}
  button{padding:8px 12px;border:0;border-radius:10px;background:#111827;color:#fff;font-weight:600;cursor:pointer}
  .row{display:flex;gap:8px}
  ul{list-style:none;margin:0;padding:0}
  li{display:flex;align-items:center;justify-content:space-between;padding:10px;border-bottom:1px solid #f1f5f9}
  .title{flex:1;margin:0 10px}
  .done{text-decoration:line-through;color:#6b7280}
  .chip{background:#eef2ff;color:#3730a3;border-radius:999px;padding:2px 10px;font-size:12px}
  .danger{background:#dc2626}
  .right{display:flex;gap:6px}
  a.logout{color:#111827;text-decoration:none;font-weight:600}
</style>
</head>
<body>
<header>
  <div>
    <h1>To-Do</h1>
    <div class="muted">Connecté en tant que <b><?= htmlspecialchars($user) ?></b></div>
  </div>
  <div class="right">
    <span class="chip"><?= $openCount ?> / <?= $totalCount ?> à faire</span>
    <a class="logout" href="logout.php">Logout</a>
  </div>
</header>

<main>
  <div class="card">
    <form method="post" class="row">
      <input type="hidden" name="action" value="add">
      <input type="text" name="title" placeholder="Ajouter une tâche..." required>
      <button type="submit">Ajouter</button>
    </form>
  </div>

  <div class="card">
    <?php if (empty($tasks)): ?>
      <p class="muted">Aucune tâche pour le moment.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($tasks as $t): ?>
          <li>
            <form method="post" class="inline" style="margin:0">
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
              <button type="submit" title="Terminer / Rouvrir"><?= $t['done'] ? '↩︎' : '✓' ?></button>
            </form>

            <div class="title <?= $t['done'] ? 'done':'' ?>">
              <?= htmlspecialchars($t['title']) ?>
            </div>

            <form method="post" class="inline" style="margin:0">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
              <button type="submit" class="danger" title="Supprimer">🗑</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>

      <form method="post" style="margin-top:12px">
        <input type="hidden" name="action" value="clear">
        <button type="submit" class="danger">Tout effacer</button>
      </form>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
