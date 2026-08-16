<?php
if(session_status()===PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Project Management Platform</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>

<header>
    <h2>Project Management Platform</h2>
    <?php if(isset($_SESSION['user_id'])): ?>
        <p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (<?= $_SESSION['role'] ?>) | 
        <a href="../controllers/LogoutController.php">Logout</a></p>
    <?php else: ?>
        <p><a href="/views/login.php">Login</a></p>
    <?php endif; ?>
</header>

<hr>