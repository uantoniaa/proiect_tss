<?php
require_once 'AuthService.php';
session_start();

$authService = new AuthService();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $message = $authService->register($username, $password);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Înregistrare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Înregistrare</h1>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Utilizator" required><br>
            <input type="password" name="password" placeholder="Parolă" required><br>
            <button type="submit">Creează cont</button>
            <p>Ai deja cont? <a href="login.php">Autentifică-te</a></p>
        </form>
    </div>
</body>
</html>
