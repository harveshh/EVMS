<?php
require_once 'config.php';
$base = '';
$error = '';

if (!isset($_SESSION['reset_email'])) {
    header('Location: forgot_password.php');
    exit;
}

$email = $_SESSION['reset_email'];
$emailEsc = mysqli_real_escape_string($conn, $email);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = clean($conn, $_POST['otp']);

    $res = mysqli_query($conn, "SELECT * FROM password_resets
                                 WHERE email = '$emailEsc' AND otp = '$otp' AND expires_at > NOW()");

    if ($res && mysqli_num_rows($res) > 0) {
        $_SESSION['otp_verified'] = true;
        header('Location: reset_password.php');
        exit;
    } else {
        $error = 'That code is invalid or has expired. Please try again or request a new one.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <h2 class="center">Enter the code</h2>
      <p class="form-note" style="margin-top:-8px;">We sent a 6-digit code to <?php echo htmlspecialchars($email); ?>. It expires in 10 minutes.</p>
      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post" class="validate">
        <div class="form-row">
          <label>6-digit code</label>
          <input type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
        </div>
        <button type="submit" class="btn btn-brass btn-block">Verify</button>
      </form>
      <p class="form-note"><a href="forgot_password.php">Didn't get a code? Send again</a></p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
