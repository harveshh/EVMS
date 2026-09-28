<?php
require_once 'config.php';
$base = '';

if (!isLoggedIn()) { header('Location: login.php'); exit; }
$userId = $_SESSION['user_id'];

// Handle cancellation
if (isset($_GET['cancel'])) {
    $bid = (int) $_GET['cancel'];
    $b = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $bid AND user_id = $userId"));
    if ($b && $b['booking_status'] !== 'Cancelled') {
        mysqli_query($conn, "UPDATE bookings SET booking_status = 'Cancelled' WHERE booking_id = $bid");
        mysqli_query($conn, "UPDATE venues SET availability = 'Available' WHERE venue_id = {$b['venue_id']}");
    }
    header('Location: my_bookings.php');
    exit;
}

$res = mysqli_query($conn, "
    SELECT b.*, v.venue_name, v.price_per_hour, e.event_type, e.guest_count
    FROM bookings b
    JOIN venues v ON b.venue_id = v.venue_id
    JOIN events e ON b.event_id = e.event_id
    WHERE b.user_id = $userId
    ORDER BY b.event_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My bookings — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <div><h2>My bookings</h2><p>Track confirmations, payments and event dates.</p></div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Venue</th><th>Event</th><th>Date</th><th>Guests</th><th>Hours</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($res) === 0): ?>
          <tr><td colspan="7">You haven't booked a venue yet. <a href="index.php">Browse venues</a></td></tr>
        <?php endif; ?>
        <?php while ($b = mysqli_fetch_assoc($res)): ?>
          <tr>
            <td><?php echo htmlspecialchars($b['venue_name']); ?></td>
            <td><?php echo htmlspecialchars($b['event_type']); ?></td>
            <td><?php echo htmlspecialchars($b['event_date']); ?></td>
            <td><?php echo (int) $b['guest_count']; ?></td>
            <td><?php echo (int) $b['duration_hours']; ?></td>
            <td>
              <span class="status-tag status-<?php echo $b['booking_status'] === 'Confirmed' ? 'Available' : 'Booked'; ?>">
                <?php echo htmlspecialchars($b['booking_status']); ?>
              </span>
            </td>
            <td>
              <?php if ($b['booking_status'] === 'Pending'): ?>
                <a href="payment.php?booking_id=<?php echo $b['booking_id']; ?>" class="btn btn-small btn-brass">Pay now</a>
              <?php elseif ($b['booking_status'] === 'Confirmed'): ?>
                <a href="my_bookings.php?cancel=<?php echo $b['booking_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Cancel this booking?');">Cancel</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
