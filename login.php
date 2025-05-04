<?php
require_once 'AuthService.php';
session_start();

$authService = new AuthService();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($authService->authenticate($username, $password)) {
        $_SESSION['userId'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $message = "Autentificare eșuată.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Login</h1>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Utilizator" required><br>
            <input type="password" name="password" placeholder="Parolă" required><br>
            <button type="submit">Autentificare</button>
            <p>Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
        </form>
    </div>
</body>
</html>
