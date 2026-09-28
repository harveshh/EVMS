<?php
require_once 'config.php';
$base = '';
$error = '';
$ok = false;

$typesResult = mysqli_query($conn, "SELECT DISTINCT venue_type FROM venues ORDER BY venue_type");
$existingTypes = [];
while ($t = mysqli_fetch_assoc($typesResult)) { $existingTypes[] = $t['venue_type']; }
if (!$existingTypes) { $existingTypes = ['Banquet Hall', 'Lawn', 'Conference Hall', 'Terrace', 'Studio']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ownerName  = clean($conn, $_POST['owner_name']);
    $email      = clean($conn, $_POST['email']);
    $contact    = clean($conn, $_POST['contact_number']);
    $venueName  = clean($conn, $_POST['venue_name']);
    $location   = clean($conn, $_POST['location']);
    $venueType  = clean($conn, $_POST['venue_type']);
    $capacity   = (int) $_POST['capacity'];
    $message    = clean($conn, $_POST['message']);

    if ($ownerName === '' || $email === '' || $venueName === '' || $location === '') {
        $error = 'Please fill in your name, email, venue name and location.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        mysqli_query($conn, "INSERT INTO venue_leads (owner_name, email, contact_number, venue_name, location, venue_type, capacity, message, date_submitted)
                              VALUES ('$ownerName', '$email', '$contact', '$venueName', '$location', '$venueType', $capacity, '$message', NOW())");
        $ok = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>List Your Venue — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="hero-banner" style="padding:64px 0 44px;">
  <div class="hero-banner-media" style="background-image:url('images/venues/pearl.jpg?v=<?php echo filemtime(__DIR__ . '/images/venues/pearl.jpg'); ?>');"></div>
  <div class="hero-banner-overlay"></div>
  <div class="container hero-banner-content" style="max-width:640px;">
    <p class="eyebrow">For Venue Owners</p>
    <h1 style="font-size:38px;">List Your Venue on VenuéWest</h1>
    <p class="lede">Reach couples, planners and companies actively searching for a space like yours. Tell us a bit about your venue and our team will follow up to get you listed.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:720px;">
    <div class="form-card" style="max-width:100%;">
      <?php if ($ok): ?>
        <div class="alert alert-ok">Thanks, <?php echo htmlspecialchars($ownerName); ?>! We've received your venue details and our team will reach out within 2 business days.</div>
      <?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

      <form method="post" class="validate">
        <div class="form-grid-2">
          <div class="form-row"><label>Your name</label><input type="text" name="owner_name" required></div>
          <div class="form-row"><label>Email address</label><input type="email" name="email" required></div>
        </div>
        <div class="form-grid-2">
          <div class="form-row"><label>Contact number</label><input type="text" name="contact_number"></div>
          <div class="form-row"><label>Capacity (guests)</label><input type="number" name="capacity" min="1"></div>
        </div>
        <div class="form-row"><label>Venue name</label><input type="text" name="venue_name" required></div>
        <div class="form-grid-2">
          <div class="form-row"><label>Location</label><input type="text" name="location" required></div>
          <div class="form-row">
            <label>Venue type</label>
            <select name="venue_type">
              <?php foreach ($existingTypes as $vt): ?>
                <option value="<?php echo htmlspecialchars($vt); ?>"><?php echo htmlspecialchars($vt); ?></option>
              <?php endforeach; ?>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
        <div class="form-row"><label>Tell us about your venue</label><textarea name="message" rows="4" placeholder="Amenities, availability, pricing expectations..."></textarea></div>
        <button type="submit" class="btn btn-brass btn-block">Submit Venue Details</button>
        <p class="form-note">Prefer to talk first? <a href="contact.php">Contact our team</a> directly.</p>
      </form>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
