<?php$
// Start the session before including any other PHP files.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

$message = '';


function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $name    = trim($_POST['customer_name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $vehicle = trim($_POST['vehicle'] ?? '');
    $service = trim($_POST['service_name'] ?? '');
    $date    = trim($_POST['service_date'] ?? '');

    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $vehicle === '' ||
        $service === '' ||
        $date === ''
    ) {
        $message = 'Please complete every field with valid details.';
    } else {
        $dateObject = DateTime::createFromFormat('!Y-m-d', $date);
        $dateErrors = DateTime::getLastErrors();

        if (
            !$dateObject ||
            ($dateErrors !== false &&
                ($dateErrors['warning_count'] > 0 ||
                 $dateErrors['error_count'] > 0)) ||
            $dateObject->format('Y-m-d') !== $date
        ) {
            $message = 'Please select a valid service date.';
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO appointments
                (customer_name, email, vehicle, service_name, service_date)
                VALUES (?, ?, ?, ?, ?)"
            );

            if ($stmt) {
                $stmt->bind_param(
                    'sssss',
                    $name,
                    $email,
                    $vehicle,
                    $service,
                    $date
                );

                if ($stmt->execute()) {
                    $message = 'Appointment booked successfully!';
                } else {
                    error_log('Appointment insert failed: ' . $stmt->error);
                    $message = 'Unable to book appointment. Please try again.';
                }

                $stmt->close();
            } else {
                error_log('Appointment prepare failed: ' . $conn->error);
                $message = 'Unable to process your booking right now.';
            }
        }
    }
}

$appointments = $conn->query(
    "SELECT id, customer_name, email, vehicle, service_name,
            service_date, status
     FROM appointments
     ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vehicle Service Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Vehicle Service Management System</h1>
    <p>Fast, reliable and affordable vehicle service booking</p>
</header>

<nav class="navbar">
    <a href="index.php">Home</a>
    <a href="#booking">Book a service</a>
    <a href="register.php">Customer Register</a>
    <a href="login.php">Customer Login</a>
    <a href="admin-login.php">Admin Login</a>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="my-appointments.php">My Appointments</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</nav>

<main>
    <section class="panel" id="booking">
        <h2>Book a Service</h2>

        <?php if ($message !== ''): ?>
            <p class="notice"><?= e($message) ?></p>
        <?php endif; ?>

        <form method="post" action="index.php#booking">
            <label>
                Customer name
                <input type="text" name="customer_name"
                       required maxlength="100"
                       value="<?= e($_POST['customer_name'] ?? '') ?>">
            </label>

            <label>
                Email
                <input type="email" name="email"
                       required maxlength="150"
                       value="<?= e($_POST['email'] ?? '') ?>">
            </label>

            <label>
                Vehicle model / number
                <input type="text" name="vehicle"
                       required maxlength="120"
                       value="<?= e($_POST['vehicle'] ?? '') ?>">
            </label>

            <label>
                Service
                <select name="service_name" required>
                    <option value="">Choose a service</option>
                    <?php
                    $services = [
                        'Oil Change',
                        'Full Service',
                        'Tire Replacement',
                        'Brake Check',
                        'Engine Check'
                    ];

                    foreach ($services as $item):
                    ?>
                        <option value="<?= e($item) ?>"
                            <?= (($_POST['service_name'] ?? '') === $item)
                                ? 'selected' : '' ?>>
                            <?= e($item) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Preferred date
                <input type="date" name="service_date" required
                       value="<?= e($_POST['service_date'] ?? '') ?>">
            </label>

            <button type="submit" name="book" value="1">
                Book Appointment
            </button>
        </form>
    </section>

    <section class="panel">
        <h2>Appointments</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Vehicle</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments && $appointments->num_rows > 0): ?>
                        <?php while ($row = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($row['id']) ?></td>
                                <td><?= e($row['customer_name']) ?></td>
                                <td><?= e($row['email']) ?></td>
                                <td><?= e($row['vehicle']) ?></td>
                                <td><?= e($row['service_name']) ?></td>
                                <td><?= e($row['service_date']) ?></td>
                                <td>
                                    <span class="status">
                                        <?= e($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No appointments yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="small">
            Register or sign in to view your customer account.
            Administrators should use the separate admin login.
        </p>
    </section>
</main>

</body>
</html>
