<!DOCTYPE html>
<html>
<head>
    <title>Login - Project Management</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 300px; margin: auto; }
        input { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 8px 16px; }
        p.error { color: red; }
    </style>
</head>
<body>

<h2>Login</h2>

<?php if(!empty($error)): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="../controllers/LoginController.php">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

</body>
</html>