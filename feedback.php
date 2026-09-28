<?php
require_once 'config.php';
$base = '';

if (!isLoggedIn()) { header('Location: login.php'); exit; }
$userId = $_SESSION['user_id'];
$error = ''; $ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venueId = (int) $_POST['venue_id'];
    $rating = (int) $_POST['rating'];
    $comments = clean($conn, $_POST['comments']);

    // Only allow feedback for venues the user has actually booked
    $owns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT booking_id FROM bookings WHERE user_id = $userId AND venue_id = $venueId AND booking_status = 'Confirmed'"));

    if (!$owns) {
        $error = 'You can only review venues you have booked and confirmed.';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Please choose a rating between 1 and 5.';
    } else {
        $today = date('Y-m-d');
        mysqli_query($conn, "INSERT INTO feedback (user_id, venue_id, rating, comments, date_posted)
                              VALUES ($userId, $venueId, $rating, '$comments', '$today')");
        $ok = 'Thanks — your review has been posted.';
    }
}

// Venues eligible for review: confirmed bookings for this user
$eligible = mysqli_query($conn, "
    SELECT DISTINCT v.venue_id, v.venue_name
    FROM bookings b JOIN venues v ON b.venue_id = v.venue_id
    WHERE b.user_id = $userId AND b.booking_status = 'Confirmed'
");

$myReviews = mysqli_query($conn, "
    SELECT f.*, v.venue_name FROM feedback f JOIN venues v ON f.venue_id = v.venue_id
    WHERE f.user_id = $userId ORDER BY f.date_posted DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reviews — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container" style="display:grid; grid-template-columns:1fr 1fr; gap:40px;">
    <div>
      <h2>Leave a review</h2>
      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if ($ok): ?><div class="alert alert-ok"><?php echo htmlspecialchars($ok); ?></div><?php endif; ?>

      <?php if (mysqli_num_rows($eligible) === 0): ?>
        <p>You can review a venue once you've completed a confirmed booking there.</p>
      <?php else: ?>
        <form method="post" class="validate form-card" style="max-width:100%;">
          <div class="form-row">
            <label>Venue</label>
            <select name="venue_id" required>
              <?php while ($v = mysqli_fetch_assoc($eligible)): ?>
                <option value="<?php echo $v['venue_id']; ?>"><?php echo htmlspecialchars($v['venue_name']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-row">
            <label>Rating</label>
            <select name="rating" required>
              <option value="5">★★★★★ Excellent</option>
              <option value="4">★★★★ Good</option>
              <option value="3">★★★ Average</option>
              <option value="2">★★ Poor</option>
              <option value="1">★ Very poor</option>
            </select>
          </div>
          <div class="form-row">
            <label>Comments</label>
            <textarea name="comments" rows="4" required placeholder="How was the venue, service, and overall experience?"></textarea>
          </div>
          <button type="submit" class="btn btn-brass btn-block">Post review</button>
        </form>
      <?php endif; ?>
    </div>

    <div>
      <h2>My reviews</h2>
      <?php if (mysqli_num_rows($myReviews) === 0): ?>
        <p style="color:var(--ink-soft);">You haven't posted any reviews yet.</p>
      <?php endif; ?>
      <?php while ($r = mysqli_fetch_assoc($myReviews)): ?>
        <div style="border-bottom:1px solid var(--line); padding:14px 0;">
          <strong><?php echo htmlspecialchars($r['venue_name']); ?></strong>
          <span class="rating-stars"><?php echo str_repeat('★', (int) $r['rating']); ?></span>
          <p style="margin:6px 0 0;"><?php echo htmlspecialchars($r['comments']); ?></p>
          <span style="font-size:12.5px; color:var(--ink-soft);"><?php echo htmlspecialchars($r['date_posted']); ?></span>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
