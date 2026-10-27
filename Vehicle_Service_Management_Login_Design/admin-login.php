<?php
session_start();
$msg = '';
$adminUser = getenv('ADMIN_USERNAME') ?: 'admin';
$adminPass = getenv('ADMIN_PASSWORD') ?: 'VehicleAdmin@2026';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if (hash_equals($adminUser, $u) && hash_equals($adminPass, $p)) {
        session_regenerate_id(true);
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_name'] = 'Administrator';
        header('Location: admin-dashboard.php');
        exit;
    }
    $msg = 'Incorrect admin credentials. Please try again.';
}
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Portal | VehicleCare</title><link rel="stylesheet" href="style.css">
</head>
<body class="auth-page admin-auth">
<nav class="navbar auth-nav">
  <a class="brand" href="index.php"><span class="brand-mark">VC</span><span>VehicleCare</span></a>
  <div class="nav-links"><a href="index.php">Customer website</a><a href="login.php">Customer login</a></div>
</nav>
<main class="auth-main admin-auth-main">
  <aside class="auth-promo admin-promo">
    <div class="eyebrow"><span class="eyebrow-dot"></span> STAFF ACCESS</div>
    <h1>Manage every<br><span>mile.</span></h1>
    <p>Access the service operations workspace to review bookings and update appointment progress.</p>
    <div class="promo-points">
      <div><span class="point-icon">✓</span><span>Review all appointments</span></div>
      <div><span class="point-icon">✓</span><span>Update service status</span></div>
      <div><span class="point-icon">✓</span><span>Keep service records organized</span></div>
    </div>
    <div class="promo-orbit orbit-one"></div><div class="promo-orbit orbit-two"></div>
  </aside>
  <section class="auth-card">
    <div class="auth-icon admin-icon" aria-hidden="true">⌘</div>
    <p class="auth-kicker">ADMINISTRATOR PORTAL</p>
    <h2>Admin sign in</h2>
    <p class="auth-subtitle">This area is for authorized staff only.</p>
    <?php if ($msg): ?><p class="error"><?=e($msg)?></p><?php endif; ?>
    <form method="post" class="auth-form">
      <label>Admin username<input name="username" autocomplete="username" placeholder="Enter admin username" required></label>
      <label>Password<input type="password" name="password" autocomplete="current-password" placeholder="Enter admin password" required></label>
      <button type="submit">Open dashboard <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch"><a href="index.php">← Return to customer website</a></p>
    <div class="auth-security"><span>●</span> Authorized access only. Change demo credentials before public deployment.</div>
  </section>
</main>
<footer>© 2026 VehicleCare · Administration portal</footer>
</body></html>
