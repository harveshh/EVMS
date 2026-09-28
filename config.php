<?php
/**
 * Database configuration
 * Update these values if your MySQL setup is different (default XAMPP values shown).
 */
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'evms');


$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Self-healing tables for the newer Contact and List-Your-Venue features,
// so upgrading an older install (without re-importing schema.sql) still works.
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    email VARCHAR(80) NOT NULL,
    subject VARCHAR(120),
    message VARCHAR(500) NOT NULL,
    date_sent DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'New'
)");
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS venue_leads (
    lead_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_name VARCHAR(80) NOT NULL,
    email VARCHAR(80) NOT NULL,
    contact_number VARCHAR(20),
    venue_name VARCHAR(120) NOT NULL,
    location VARCHAR(120),
    venue_type VARCHAR(50),
    capacity INT,
    message VARCHAR(500),
    date_submitted DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'New'
)");
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS password_resets (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(80) NOT NULL,
    otp VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL
)");

/**
 * Mail settings for the "Forgot password" OTP feature (sent via SMTP).
 *
 * Steps for Gmail:
 *  1. Turn ON 2-Step Verification: https://myaccount.google.com/security
 *  2. Create an App Password:      https://myaccount.google.com/apppasswords
 *  3. Paste your Gmail below and the 16-character App Password (NOT your normal password).
 *
 * The OTP is always sent to the email the customer registered with.
 */
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', '');       // e.g. 'yourname@gmail.com'  <-- FILL THIS
define('MAIL_PASSWORD', '');       // e.g. 'abcd efgh ijkl mnop' <-- FILL THIS (App Password)
define('MAIL_PORT', 587);          // 587 = STARTTLS (recommended), 465 = SSL
define('MAIL_FROM_NAME', 'VenuéWest');
define('MAIL_VERIFY_SSL', false);  // false avoids certificate errors on local XAMPP; set true on a real server

require_once __DIR__ . '/includes/mailer.php';

// Helper: is a customer logged in?
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper: is an admin logged in?
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Helper: escape + trim shortcut
function clean($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}
