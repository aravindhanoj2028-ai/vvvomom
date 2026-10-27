<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';
$msg = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) {
        $msg = 'Enter your name, a valid email, and a password with at least 8 characters.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $st = $conn->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'customer')");
        $st->bind_param('sss', $name, $email, $hash);
        if ($st->execute()) { $msg = 'Your account was created. You can now sign in.'; $success = true; }
        else { $msg = 'This email may already be registered. Try signing in instead.'; }
        $st->close();
    }
}
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Account | VehicleCare</title><link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<nav class="navbar auth-nav">
  <a class="brand" href="index.php"><span class="brand-mark">VC</span><span>VehicleCare</span></a>
  <div class="nav-links"><a href="index.php">Home</a><a href="login.php">Customer login</a><a href="admin-login.php">Admin portal</a></div>
</nav>
<main class="auth-main">
  <aside class="auth-promo">
    <div class="eyebrow"><span class="eyebrow-dot"></span> START WITH VEHICLECARE</div>
    <h1>Better care<br>starts <span>here.</span></h1>
    <p>Create an account to keep your vehicle service details and appointments in one convenient place.</p>
    <div class="promo-points">
      <div><span class="point-icon">✓</span><span>Easy appointment access</span></div>
      <div><span class="point-icon">✓</span><span>Personal service history</span></div>
      <div><span class="point-icon">✓</span><span>Simple account management</span></div>
    </div>
    <div class="promo-orbit orbit-one"></div><div class="promo-orbit orbit-two"></div>
  </aside>
  <section class="auth-card">
    <div class="auth-icon" aria-hidden="true">＋</div>
    <p class="auth-kicker">NEW CUSTOMER</p>
    <h2>Create your account</h2>
    <p class="auth-subtitle">It only takes a moment to get started.</p>
    <?php if ($msg): ?><p class="<?= $success ? 'notice' : 'error' ?>"><?=e($msg)?></p><?php endif; ?>
    <form method="post" class="auth-form">
      <label>Full name<input name="name" autocomplete="name" placeholder="Your full name" required maxlength="100"></label>
      <label>Email address<input type="email" name="email" autocomplete="email" placeholder="you@example.com" required maxlength="150"></label>
      <label>Password <span class="label-hint">At least 8 characters</span><input type="password" name="password" autocomplete="new-password" placeholder="Create a strong password" minlength="8" required></label>
      <button type="submit">Create account <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch">Already registered? <a href="login.php">Sign in</a></p>
  </section>
</main>
<footer>© 2026 VehicleCare · Vehicle Service Management System</footer>
</body></html>
