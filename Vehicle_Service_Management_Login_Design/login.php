<?php
require_once 'db_connect.php';
session_start();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    $st = $conn->prepare("SELECT id,name,email,password_hash,role FROM users WHERE email=? AND role='customer'");
    $st->bind_param('s', $email);
    $st->execute();
    $u = $st->get_result()->fetch_assoc();
    $st->close();
    if ($u && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_name'] = $u['name'];
        $_SESSION['user_role'] = 'customer';
        header('Location: my-appointments.php');
        exit;
    }
    $msg = 'We could not sign you in. Check your email and password, then try again.';
}
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Customer Sign In | VehicleCare</title><link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<nav class="navbar auth-nav">
  <a class="brand" href="index.php"><span class="brand-mark">VC</span><span>VehicleCare</span></a>
  <div class="nav-links"><a href="index.php">Home</a><a href="register.php">Create account</a><a href="admin-login.php">Admin portal</a></div>
</nav>
<main class="auth-main">
  <aside class="auth-promo">
    <div class="eyebrow"><span class="eyebrow-dot"></span> YOUR JOURNEY, OUR CARE</div>
    <h1>Welcome<br><span>back.</span></h1>
    <p>Sign in to manage your service appointments and keep your vehicle ready for the road.</p>
    <div class="promo-points">
      <div><span class="point-icon">✓</span><span>View your service history</span></div>
      <div><span class="point-icon">✓</span><span>Track appointment status</span></div>
      <div><span class="point-icon">✓</span><span>Book your next visit easily</span></div>
    </div>
    <div class="promo-orbit orbit-one"></div><div class="promo-orbit orbit-two"></div>
  </aside>
  <section class="auth-card">
    <div class="auth-icon" aria-hidden="true">↗</div>
    <p class="auth-kicker">CUSTOMER ACCOUNT</p>
    <h2>Sign in to your account</h2>
    <p class="auth-subtitle">Enter your details below to continue.</p>
    <?php if ($msg): ?><p class="error"><?=e($msg)?></p><?php endif; ?>
    <form method="post" class="auth-form">
      <label>Email address<input type="email" name="email" autocomplete="email" placeholder="you@example.com" required></label>
      <label>Password<input type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required></label>
      <button type="submit">Sign in <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch">New to VehicleCare? <a href="register.php">Create an account</a></p>
    <div class="auth-security"><span>●</span> Your account details are handled securely.</div>
  </section>
</main>
<footer>© 2026 VehicleCare · Vehicle Service Management System</footer>
</body></html>
