<?php
// Tự động phát hiện base URL đúng với mọi port
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST']; // gồm cả port, ví dụ: localhost:2410
define('BASE_URL', $protocol . '://' . $host . '/project1');
?>
