<?php
// Redirect to public directory
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base = dirname($_SERVER['SCRIPT_NAME']);
$base = str_replace('\\', '/', $base);
$base = rtrim($base, '/');
if ($base === '.' || $base === '/') {
    $base = '';
}
header('Location: ' . $protocol . '://' . $host . $base . '/public/index.php');
exit;
?>