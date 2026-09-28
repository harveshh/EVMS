<?php
require_once 'config.php';
$base = '';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$venueId = isset($_GET['venue_id']) ? (int) $_GET['venue_id'] : (int) ($_POST['venue_id'] ?? 0);
$venue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM venues WHERE venue_id = $venueId"));
if (!$venue) { header('Location: index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventType = clean($conn, $_POST['event_type']);
    $eventDate = clean($conn, $_POST['event_date']);
    $guestCount = (int) $_POST['guest_count'];
    $durationHours = (int) $_POST['duration_hours'];
    $notes = clean($conn, $_POST['special_notes']);
    $today = date('Y-m-d');

    if ($eventType === '' || $eventDate === '' || $guestCount <= 0 || $durationHours <= 0) {
        $error = 'Please fill in all required fields with valid values.';
    } elseif ($eventDate < $today) {
        $error = 'Event date cannot be in the past.';
    } elseif ($guestCount > $venue['capacity']) {
        $error = 'Guest count exceeds this venue\'s capacity of ' . $venue['capacity'] . '.';
    } elseif ($venue['availability'] !== 'Available') {
        $error = 'This venue is no longer available for booking.';
    } else {
        // Prevent double-booking: same venue + same event date already confirmed
        $conflict = mysqli_query($conn, "SELECT booking_id FROM bookings
                                          WHERE venue_id = $venueId AND event_date = '$eventDate'
                                          AND booking_status IN ('Pending','Confirmed')");
        if (mysqli_num_rows($conflict) > 0) {
            $error = 'This venue is already booked or pending confirmation for that date. Please choose another date.';
        } else {
            mysqli_query($conn, "INSERT INTO events (event_type, guest_count, special_notes)
                                  VALUES ('$eventType', $guestCount, '$notes')");
            $eventId = mysqli_insert_id($conn);
            $userId = $_SESSION['user_id'];

            mysqli_query($conn, "INSERT INTO bookings (user_id, venue_id, event_id, booking_date, event_date, duration_hours, booking_status)
                                  VALUES ($userId, $venueId, $eventId, '$today', '$eventDate', $durationHours, 'Pending')");
            $bookingId = mysqli_insert_id($conn);

            header("Location: payment.php?booking_id=$bookingId");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book <?php echo htmlspecialchars($venue['venue_name']); ?> — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <h2>Reserve <?php echo htmlspecialchars($venue['venue_name']); ?></h2>
      <p class="card-meta" style="margin-bottom:20px;">📍 <?php echo htmlspecialchars($venue['location']); ?> &nbsp;·&nbsp; 👥 Max <?php echo (int) $venue['capacity']; ?> guests</p>

      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

      <form method="post" class="validate">
        <input type="hidden" name="venue_id" value="<?php echo $venue['venue_id']; ?>">

        <div class="form-row">
          <label>Event type</label>
          <select name="event_type" required>
            <option value="">Select event type</option>
            <option>Wedding</option>
            <option>Birthday</option>
            <option>Corporate</option>
            <option>Conference</option>
            <option>Exhibition</option>
            <option>Other</option>
          </select>
        </div>

        <div class="form-grid-2">
          <div class="form-row">
            <label>Event date</label>
            <input type="date" name="event_date" id="event_date" data-future-only="true" min="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="form-row">
            <label>Duration (hours)</label>
            <input type="number" name="duration_hours" id="duration_hours" min="1" max="24" required>
          </div>
        </div>

        <div class="form-row">
          <label>Expected guest count</label>
          <input type="number" name="guest_count" min="1" max="<?php echo (int) $venue['capacity']; ?>" required>
        </div>

        <div class="form-row">
          <label>Special notes / requirements</label>
          <textarea name="special_notes" rows="3" placeholder="Catering, decor, seating layout, etc."></textarea>
        </div>

        <div class="alert alert-ok" style="display:flex; justify-content:space-between;">
          <span>Estimated total (<span id="price-rate" data-rate="<?php echo $venue['price_per_hour']; ?>">₹<?php echo number_format($venue['price_per_hour']); ?>/hr</span>)</span>
          <strong id="price-total">₹0</strong>
        </div>

        <button type="submit" class="btn btn-brass btn-block">Continue to payment</button>
      </form>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
