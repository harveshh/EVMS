<?php
require_once 'config.php';
$base = '';
$error = '';
$message = '';

/**
 * Emails the OTP to the address the customer registered with.
 * Returns '' on success, or an error message on failure.
 */
function sendOtpEmail($to, $otp) {
    $subject = 'Your VenuéWest password reset OTP';
    $body = "Your one-time password (OTP) is: $otp\n\n"
          . "It is valid for the next 10 minutes. If you didn't request a "
          . "password reset, you can safely ignore this email.";
    return send_mail_smtp($to, $subject, $body);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);

    $check = mysqli_query($conn, "SELECT user_id, email FROM users WHERE email = '$email'");

    if ($check && mysqli_num_rows($check) > 0) {
        $user = mysqli_fetch_assoc($check);
        $registeredEmail = $user['email'];                 // the email stored at registration
        $registeredEsc   = mysqli_real_escape_string($conn, $registeredEmail);

        $otp = strval(random_int(100000, 999999));

        // Use MySQL's clock for both saving and checking, so PHP/MySQL timezone
        // differences can never make a fresh OTP look "expired".
        mysqli_query($conn, "DELETE FROM password_resets WHERE email = '$registeredEsc'");
        mysqli_query($conn, "INSERT INTO password_resets (email, otp, expires_at, created_at)
                              VALUES ('$registeredEsc', '$otp', DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())");

        $mailError = sendOtpEmail($registeredEmail, $otp);
        if ($mailError === '') {
            $_SESSION['reset_email'] = $registeredEmail;
            unset($_SESSION['otp_verified']);
            header('Location: verify_otp.php');
            exit;
        } else {
            mysqli_query($conn, "DELETE FROM password_resets WHERE email = '$registeredEsc'");
            $error = 'Could not send the OTP email. ' . $mailError;
        }
    } else {
        // Don't reveal whether the email is registered or not
        $message = 'If that email is registered with us, an OTP has been sent to it.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <h2 class="center">Forgot your password?</h2>
      <p class="form-note" style="margin-top:-8px;">Enter your account email and we'll send you a one-time code to reset it.</p>
      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if ($message): ?><div class="alert alert-ok"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
      <form method="post" class="validate">
        <div class="form-row">
          <label>Email address</label>
          <input type="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-brass btn-block">Send OTP</button>
      </form>
      <p class="form-note"><a href="login.php">Back to log in</a></p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
