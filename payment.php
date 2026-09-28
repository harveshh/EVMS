<?php
require_once 'config.php';
$base = '';

if (!isLoggedIn()) { header('Location: login.php'); exit; }

$bookingId = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : (int) ($_POST['booking_id'] ?? 0);

$booking = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT b.*, v.venue_name, v.price_per_hour, v.venue_id
    FROM bookings b JOIN venues v ON b.venue_id = v.venue_id
    WHERE b.booking_id = $bookingId AND b.user_id = {$_SESSION['user_id']}
"));

if (!$booking) { header('Location: my_bookings.php'); exit; }

$amount = $booking['price_per_hour'] * $booking['duration_hours'];
$error = '';
$success = false;

if ($booking['booking_status'] === 'Confirmed') {
    $success = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$success) {
    $method = clean($conn, $_POST['payment_method']);
    $cardNumber = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');

    if ($method === '') {
        $error = 'Please choose a payment method.';
    } elseif ($method === 'Card' && !preg_match('/^\d{12,16}$/', $cardNumber)) {
        $error = 'Please enter a valid card number (12–16 digits).';
    } else {
        $today = date('Y-m-d');
        mysqli_query($conn, "INSERT INTO payments (booking_id, amount, payment_method, payment_date, payment_status)
                              VALUES ($bookingId, $amount, '$method', '$today', 'Paid')");
        mysqli_query($conn, "UPDATE bookings SET booking_status = 'Confirmed' WHERE booking_id = $bookingId");
        mysqli_query($conn, "UPDATE venues SET availability = 'Booked' WHERE venue_id = {$booking['venue_id']}");
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <?php if ($success): ?>
        <div class="alert alert-ok">Payment received — your booking is confirmed.</div>
        <h2>Booking receipt</h2>
        <table style="margin-bottom:20px;">
          <tr><td>Venue</td><td><strong><?php echo htmlspecialchars($booking['venue_name']); ?></strong></td></tr>
          <tr><td>Event date</td><td><?php echo htmlspecialchars($booking['event_date']); ?></td></tr>
          <tr><td>Duration</td><td><?php echo (int) $booking['duration_hours']; ?> hour(s)</td></tr>
          <tr><td>Amount paid</td><td><strong>₹<?php echo number_format($amount); ?></strong></td></tr>
          <tr><td>Status</td><td><span class="status-tag status-Available">Confirmed</span></td></tr>
        </table>
        <a href="my_bookings.php" class="btn btn-brass btn-block">View my bookings</a>
      <?php else: ?>
        <h2>Complete your payment</h2>
        <p class="card-meta" style="margin-bottom:20px;">Booking for <strong><?php echo htmlspecialchars($booking['venue_name']); ?></strong> on <?php echo htmlspecialchars($booking['event_date']); ?></p>

        <div class="alert alert-ok" style="display:flex; justify-content:space-between;">
          <span>Amount due</span>
          <strong>₹<?php echo number_format($amount); ?></strong>
        </div>

        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <form method="post" class="validate">
          <input type="hidden" name="booking_id" value="<?php echo $bookingId; ?>">
          <div class="form-row">
            <label>Payment method</label>
            <select name="payment_method" id="pay-method" required>
              <option value="">Select method</option>
              <option value="Card">Debit / Credit card</option>
              <option value="UPI">UPI</option>
              <option value="Netbanking">Netbanking</option>
            </select>
          </div>
          <div class="form-row" id="card-field">
            <label>Card number</label>
            <input type="text" name="card_number" placeholder="1234 5678 9012 3456">
          </div>
          <button type="submit" class="btn btn-brass btn-block">Pay ₹<?php echo number_format($amount); ?></button>
          <p class="form-note">This is a simulated payment for demonstration — no real transaction is processed.</p>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
