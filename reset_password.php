<?php
require_once 'config.php';
$base = '';
$error = '';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header('Location: forgot_password.php');
    exit;
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password === '' || $confirm === '') {
        $error = 'Please fill in both password fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $emailEsc = clean($conn, $email);
        mysqli_query($conn, "UPDATE users SET password = '$hash' WHERE email = '$emailEsc'");
        mysqli_query($conn, "DELETE FROM password_resets WHERE email = '$emailEsc'");

        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_verified']);

        header('Location: login.php?reset=success');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <h2 class="center">Set a new password</h2>
      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post" class="validate">
        <div class="form-row">
          <label>New password</label>
          <input type="password" name="password" required data-minlength="6">
        </div>
        <div class="form-row">
          <label>Confirm new password</label>
          <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-brass btn-block">Reset password</button>
      </form>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
