<?php
require_once 'config.php';
$base = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($conn, $_POST['name']);
    $email = clean($conn, $_POST['email']);
    $contact = clean($conn, $_POST['contact_number']);
    $address = clean($conn, $_POST['address']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Name, email and password are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $today = date('Y-m-d');
            $ok = mysqli_query($conn, "INSERT INTO users (name, email, contact_number, password, address, date_registered)
                                        VALUES ('$name', '$email', '$contact', '$hash', '$address', '$today')");
            if ($ok) {
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['user_name'] = $name;
                header('Location: index.php');
                exit;
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign up — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <h2 class="center">Create your account</h2>
      <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post" class="validate">
        <div class="form-row">
          <label>Full name</label>
          <input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        <div class="form-row">
          <label>Email address</label>
          <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-row">
          <label>Contact number</label>
          <input type="text" name="contact_number" pattern="[0-9]{10,15}" title="Numbers only">
        </div>
        <div class="form-row">
          <label>Address</label>
          <input type="text" name="address">
        </div>
        <div class="form-grid-2">
          <div class="form-row">
            <label>Password</label>
            <input type="password" name="password" required data-minlength="6">
          </div>
          <div class="form-row">
            <label>Confirm password</label>
            <input type="password" name="confirm_password" required>
          </div>
        </div>
        <button type="submit" class="btn btn-brass btn-block">Sign up</button>
      </form>
      <p class="form-note">Already have an account? <a href="login.php">Log in</a></p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
