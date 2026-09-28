<?php
require_once 'config.php';
$base = '';

$venueCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM venues"))['c'];
$ownerCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM venue_owners"))['c'];
$bookingCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM bookings"))['c'];
$cityCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT location) c FROM venues"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us — VenuéWest</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="hero-banner" style="padding:70px 0 46px;">
  <div class="hero-banner-media" style="background-image:url('images/venues/pavilion.jpg?v=<?php echo filemtime(__DIR__ . '/images/venues/pavilion.jpg'); ?>');"></div>
  <div class="hero-banner-overlay"></div>
  <div class="container hero-banner-content" style="max-width:640px;">
    <p class="eyebrow">About VenuéWest</p>
    <h1 style="font-size:40px;">More Than Just a Place, It's a Feeling</h1>
    <p class="lede">We built VenuéWest so that finding, comparing and booking the right venue takes minutes, not weeks of phone calls.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center;">
      <div>
        <h2>Our Story</h2>
        <p>VenuéWest started with a simple frustration: booking a venue for a wedding, a birthday, or a corporate offsite meant endless calls, unclear pricing and no easy way to check real availability. We set out to fix that by putting verified venues, transparent pricing and instant booking in one place.</p>
        <p>Today, venue owners list their spaces with real-time availability, and customers can search, compare and confirm a booking online — with secure payment and a review system that keeps everyone honest.</p>
      </div>
      <div class="stat-grid" style="grid-template-columns:repeat(2,1fr);">
        <div class="stat-card"><span class="num"><?php echo (int) $venueCount; ?>+</span><span class="label">Listed venues</span></div>
        <div class="stat-card"><span class="num"><?php echo (int) $ownerCount; ?>+</span><span class="label">Venue partners</span></div>
        <div class="stat-card"><span class="num"><?php echo (int) $bookingCount; ?>+</span><span class="label">Bookings made</span></div>
        <div class="stat-card"><span class="num"><?php echo (int) $cityCount; ?>+</span><span class="label">Locations covered</span></div>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background:var(--ivory-dim); border-top:1px solid var(--line); border-bottom:1px solid var(--line);">
  <div class="container">
    <div class="section-intro">
      <h2>What We Stand For</h2>
      <p>The principles behind every booking on VenuéWest</p>
    </div>
    <div class="testimonial-grid">
      <div class="testimonial-card">
        <div class="step-icon" style="margin:0 0 16px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v5c0 5-3 8.5-7 10-4-1.5-7-5-7-10V6l7-3Z" stroke="currentColor" stroke-width="1.6"/></svg></div>
        <h3 style="font-size:18px;">Verified &amp; Transparent</h3>
        <p class="testimonial-quote" style="margin-bottom:0;">Every venue is checked before it goes live, and pricing is shown upfront — no hidden fees.</p>
      </div>
      <div class="testimonial-card">
        <div class="step-icon" style="margin:0 0 16px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M4 9.5h16" stroke="currentColor" stroke-width="1.8"/></svg></div>
        <h3 style="font-size:18px;">Real-Time Availability</h3>
        <p class="testimonial-quote" style="margin-bottom:0;">See what's actually open and book it instantly, with automatic double-booking protection.</p>
      </div>
      <div class="testimonial-card">
        <div class="step-icon" style="margin:0 0 16px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 20c0-3.3 2.5-6 5.5-6s5.5 2.7 5.5 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></div>
        <h3 style="font-size:18px;">Support That Shows Up</h3>
        <p class="testimonial-quote" style="margin-bottom:0;">From your first search to the day of your event, our team is a message away.</p>
      </div>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="cta-band-media" style="background-image:url('images/venues/lawn.jpg?v=<?php echo filemtime(__DIR__ . '/images/venues/lawn.jpg'); ?>');"></div>
  <div class="cta-band-overlay"></div>
  <div class="container cta-band-content">
    <h2>Have a Venue to List?</h2>
    <p>Join our growing network of venue owners across the country.</p>
    <a href="list_venue.php" class="btn btn-brass">List Your Venue →</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
