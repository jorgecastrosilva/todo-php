<?php
// Fonctions utilitaires (logs + stockage)
function ensure_dir($dir) {
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

function client_ip() {
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    // Si XFF contient plusieurs IP, on prend la première
    if (strpos($ip, ',') !== false) $ip = trim(explode(',', $ip)[0]);
    return $ip;
}

function log_event($event, $username = null, $details = []) {
    ensure_dir(__DIR__ . '/logs');
    $line = json_encode([
        'ts'     => date('c'),
        'ip'     => client_ip(),
        'user'   => $username,
        'event'  => $event,
        'details'=> $details
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    file_put_contents(__DIR__ . '/logs/app.log', $line . PHP_EOL, FILE_APPEND);
}

function tasks_file($username) {
    ensure_dir(__DIR__ . '/data');
    return __DIR__ . "/data/{$username}.json";
}

function load_tasks($username) {
    $file = tasks_file($username);
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function save_tasks($username, $tasks) {
    $file = tasks_file($username);
    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function require_login() {
    session_start();
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
