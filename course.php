<?php
$queryString = $_SERVER['QUERY_STRING'] ?? '';
$target = 'course-detail.php' . ($queryString !== '' ? '?' . $queryString : '');
header('Location: ' . $target, true, 302);
exit;
