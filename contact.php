<?php
require_once 'config.php';
$base = '';
$error = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = clean($conn, $_POST['name']);
    $email   = clean($conn, $_POST['email']);
    $subject = clean($conn, $_POST['subject']);
    $message = clean($conn, $_POST['message']);

    if ($name === '' || $email === '' || $message === '') {
        $error = 'Name, email and message are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        mysqli_query($conn, "INSERT INTO contact_messages (name, email, subject, message, date_sent)
                              VALUES ('$name', '$email', '$subject', '$message', NOW())");
        $ok = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section" style="padding-top:56px;">
  <div class="container">
    <div class="section-intro">
      <p class="eyebrow on-light" style="text-align:center;">Get In Touch</p>
      <h2>Contact Us</h2>
      <p>Questions about a venue, a booking, or listing your own space? We'd love to help.</p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1.3fr; gap:40px; align-items:flex-start;">
      <div>
        <div class="stat-card" style="margin-bottom:16px;">
          <h3 style="margin-bottom:6px; font-size:17px;">Email us</h3>
          <p style="margin:0; color:var(--ink-soft);"><a href="mailto:hello@venuewest.example">hello@venuewest.example</a></p>
        </div>
        <div class="stat-card" style="margin-bottom:16px;">
          <h3 style="margin-bottom:6px; font-size:17px;">Call us</h3>
          <p style="margin:0; color:var(--ink-soft);"><a href="tel:+911204567890">+91 120 456 7890</a></p>
        </div>
        <div class="stat-card">
          <h3 style="margin-bottom:6px; font-size:17px;">Visit us</h3>
          <p style="margin:0; color:var(--ink-soft);">Connaught Place, New Delhi, India<br>Mon&ndash;Sat, 10am&ndash;7pm</p>
        </div>
      </div>

      <div class="form-card" style="max-width:100%;">
        <?php if ($ok): ?>
          <div class="alert alert-ok">Thanks, <?php echo htmlspecialchars($name); ?> — your message has been sent. Our team will get back to you shortly.</div>
        <?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post" class="validate">
          <div class="form-grid-2">
            <div class="form-row"><label>Your name</label><input type="text" name="name" required></div>
            <div class="form-row"><label>Email address</label><input type="email" name="email" required></div>
          </div>
          <div class="form-row"><label>Subject</label><input type="text" name="subject" placeholder="e.g. Booking question"></div>
          <div class="form-row"><label>Message</label><textarea name="message" rows="5" required></textarea></div>
          <button type="submit" class="btn btn-brass btn-block">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
