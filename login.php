<?php
require_once 'config.php';
$base = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);
    $password = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log in — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <h2 class="center">Welcome back</h2>
      <?php if (($_GET['reset'] ?? '') === 'success'): ?><div class="alert alert-ok">Your password has been reset. Please log in with your new password.</div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post" class="validate">
        <div class="form-row">
          <label>Email address</label>
          <input type="email" name="email" required>
        </div>
        <div class="form-row">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <p class="form-note" style="margin:-8px 0 18px; text-align:right;"><a href="forgot_password.php">Forgot password?</a></p>
        <button type="submit" class="btn btn-brass btn-block">Log in</button>
      </form>
      <p class="form-note">New here? <a href="register.php">Create an account</a></p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
