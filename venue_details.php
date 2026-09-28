<?php
require_once 'config.php';
$base = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$res = mysqli_query($conn, "SELECT v.*, o.name AS owner_name, o.contact_number AS owner_contact
                             FROM venues v JOIN venue_owners o ON v.owner_id = o.owner_id
                             WHERE v.venue_id = $id");
$venue = mysqli_fetch_assoc($res);

if (!$venue) {
    header('Location: index.php');
    exit;
}

// Fetch feedback for this venue
$fbRes = mysqli_query($conn, "SELECT f.*, u.name AS user_name FROM feedback f
                               JOIN users u ON f.user_id = u.user_id
                               WHERE f.venue_id = $id ORDER BY f.date_posted DESC LIMIT 5");
$avgRes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) avg_r, COUNT(*) c FROM feedback WHERE venue_id = $id"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($venue['venue_name']); ?> — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section" style="padding-top:40px;">
  <div class="container">
    <?php
      $imgPath = __DIR__ . '/images/venues/' . $venue['image_seed'] . '.jpg';
      $hasImg  = $venue['image_seed'] && file_exists($imgPath);
      $imgFile = $hasImg ? 'images/venues/' . rawurlencode($venue['image_seed']) . '.jpg?v=' . filemtime($imgPath) : '';
    ?>
    <div class="hero-art" style="aspect-ratio:21/9; margin-bottom:28px;<?php echo $hasImg ? " background-image:linear-gradient(180deg, rgba(28,35,33,0) 40%, rgba(28,35,33,0.6) 100%), url('" . htmlspecialchars($imgFile) . "'); background-size:cover; background-position:center 35%;" : ''; ?>">
      <div class="hero-art-caption"><?php echo htmlspecialchars($venue['venue_type']); ?> · <?php echo htmlspecialchars($venue['location']); ?></div>
    </div>

    <div style="display:grid; grid-template-columns:1.6fr 1fr; gap:40px;">
      <div>
        <h1 style="font-size:34px;"><?php echo htmlspecialchars($venue['venue_name']); ?></h1>
        <p class="card-meta" style="margin-bottom:18px;">
          <span>📍 <?php echo htmlspecialchars($venue['location']); ?></span>
          <span>👥 Capacity: <?php echo (int) $venue['capacity']; ?> guests</span>
          <span class="status-tag status-<?php echo $venue['availability'] === 'Available' ? 'Available' : 'Booked'; ?>">
            <?php echo htmlspecialchars($venue['availability']); ?>
          </span>
        </p>
        <p><?php echo htmlspecialchars($venue['description']); ?></p>

        <h3 style="margin-top:32px;">Managed by</h3>
        <p><?php echo htmlspecialchars($venue['owner_name']); ?> &middot; <?php echo htmlspecialchars($venue['owner_contact']); ?></p>

        <h3 style="margin-top:32px;">Guest reviews
          <?php if ($avgRes['c'] > 0): ?>
            <span class="rating-stars"><?php echo str_repeat('★', round($avgRes['avg_r'])); ?></span>
            <span style="font-size:14px; color:var(--ink-soft);">(<?php echo $avgRes['c']; ?> review<?php echo $avgRes['c'] == 1 ? '' : 's'; ?>)</span>
          <?php endif; ?>
        </h3>
        <?php if (mysqli_num_rows($fbRes) === 0): ?>
          <p style="color:var(--ink-soft);">No reviews yet for this venue.</p>
        <?php endif; ?>
        <?php while ($fb = mysqli_fetch_assoc($fbRes)): ?>
          <div style="border-bottom:1px solid var(--line); padding:14px 0;">
            <strong><?php echo htmlspecialchars($fb['user_name']); ?></strong>
            <span class="rating-stars"><?php echo str_repeat('★', (int) $fb['rating']); ?></span>
            <p style="margin:6px 0 0;"><?php echo htmlspecialchars($fb['comments']); ?></p>
          </div>
        <?php endwhile; ?>
      </div>

      <div>
        <div class="form-card" style="max-width:100%;">
          <p style="font-size:13px; color:var(--ink-soft); margin-bottom:4px;">Starting from</p>
          <h2 style="margin-bottom:20px;">₹<?php echo number_format($venue['price_per_hour']); ?> <span style="font-size:15px; font-weight:400; font-family:var(--sans); color:var(--ink-soft);">/ hour</span></h2>
          <?php if ($venue['availability'] === 'Available'): ?>
            <a href="book.php?venue_id=<?php echo $venue['venue_id']; ?>" class="btn btn-brass btn-block">Book this venue</a>
          <?php else: ?>
            <button class="btn btn-block" disabled style="opacity:0.5;">Currently booked</button>
          <?php endif; ?>
          <?php if (!isLoggedIn()): ?>
            <p class="form-note">You'll need to <a href="login.php">log in</a> to complete a booking.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
