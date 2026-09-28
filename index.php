<?php
require_once 'config.php';
$base = '';

// ---- Search / filter handling ----
$location = isset($_GET['location']) ? clean($conn, $_GET['location']) : '';
$type     = isset($_GET['type']) ? clean($conn, $_GET['type']) : '';
$capacity = isset($_GET['capacity']) && $_GET['capacity'] !== '' ? (int) $_GET['capacity'] : 0;

$sql = "SELECT * FROM venues WHERE 1=1";
if ($location !== '') $sql .= " AND location LIKE '%$location%'";
if ($type !== '')     $sql .= " AND venue_type = '$type'";
if ($capacity > 0)    $sql .= " AND capacity >= $capacity";
$sql .= " ORDER BY venue_id DESC";
$result = mysqli_query($conn, $sql);

$typesResult = mysqli_query($conn, "SELECT DISTINCT venue_type FROM venues ORDER BY venue_type");

// ---- Testimonials: pull real 5/4-star reviews, pad with sample quotes if there aren't enough yet ----
$testimonials = [];
$fbRes = mysqli_query($conn, "
    SELECT f.rating, f.comments, u.name AS user_name
    FROM feedback f JOIN users u ON f.user_id = u.user_id
    WHERE f.rating >= 4 AND f.comments IS NOT NULL AND f.comments != ''
    ORDER BY f.rating DESC, f.date_posted DESC LIMIT 3
");
if ($fbRes) {
    while ($row = mysqli_fetch_assoc($fbRes)) {
        $testimonials[] = ['name' => $row['user_name'], 'rating' => (int) $row['rating'], 'quote' => $row['comments']];
    }
}
$fallbackTestimonials = [
    ['name' => 'Aditi Sharma', 'rating' => 5, 'quote' => 'Found the perfect wedding venue through VenuéWest. The process was so easy and hassle-free!'],
    ['name' => 'Karan Verma',  'rating' => 5, 'quote' => 'Booked a corporate event space in minutes. Great options and excellent support!'],
    ['name' => 'Neha Kapoor',  'rating' => 5, 'quote' => 'Amazing venues, transparent pricing and a super helpful team. Highly recommended!'],
];
$i = 0;
while (count($testimonials) < 3) {
    $testimonials[] = $fallbackTestimonials[$i % count($fallbackTestimonials)];
    $i++;
}

$categories = [
    ['title' => 'Weddings',          'sub' => 'Make your big day magical',        'type' => 'Lawn',            'img' => 'lawn'],
    ['title' => 'Birthday Parties',  'sub' => 'Celebrate in style',                'type' => 'Terrace',         'img' => 'rosewood'],
    ['title' => 'Corporate Events',  'sub' => 'Professional spaces for big ideas', 'type' => 'Conference Hall', 'img' => 'skyline'],
    ['title' => 'Social Gatherings', 'sub' => 'Bring people together',            'type' => 'Studio',          'img' => 'loft'],
    ['title' => 'Engagements',       'sub' => 'A new beginning',                   'type' => 'Banquet Hall',    'img' => 'pearl'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VenuéWest — Find and book event venues</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="hero-banner">
  <div class="hero-banner-media" style="background-image:url('images/venues/lawn.jpg?v=<?php echo filemtime(__DIR__ . '/images/venues/lawn.jpg'); ?>');"></div>
  <div class="hero-banner-overlay"></div>
  <div class="container hero-banner-content">
    <p class="eyebrow">Find. Book. Celebrate.</p>
    <h1>The Perfect Venue for Every Occasion</h1>
    <p class="lede">From weddings and birthdays to corporate events, find and book the best venues — all in one place.</p>

    <form class="search-pill" method="get" action="index.php#venues">
      <label class="search-field">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.1 7-12A7 7 0 0 0 5 9c0 5.9 7 12 7 12Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="1.6"/></svg>
        <input type="text" name="location" placeholder="Enter location (e.g. Delhi)" value="<?php echo htmlspecialchars($location); ?>">
      </label>
      <label class="search-field">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><rect x="3" y="4.5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M3 9.5h18M8 3v3M16 3v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <select name="type">
          <option value="">Any venue type</option>
          <?php while ($t = mysqli_fetch_assoc($typesResult)): ?>
            <option value="<?php echo htmlspecialchars($t['venue_type']); ?>" <?php echo $type === $t['venue_type'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($t['venue_type']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </label>
      <label class="search-field">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 20c0-3.3 2.5-6 5.5-6s5.5 2.7 5.5 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M16 8.2c1.4.3 2.5 1.6 2.5 3.1 0 1.2-.6 2.2-1.6 2.8M17.5 14.4c2 .5 3.5 2.6 3.5 5.1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <select name="capacity">
          <option value="">No. of guests</option>
          <option value="100" <?php echo $capacity === 100 ? 'selected' : ''; ?>>100+ guests</option>
          <option value="300" <?php echo $capacity === 300 ? 'selected' : ''; ?>>300+ guests</option>
          <option value="500" <?php echo $capacity === 500 ? 'selected' : ''; ?>>500+ guests</option>
        </select>
      </label>
      <button type="submit" class="btn btn-ink">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        Search Venues
      </button>
    </form>

    <div class="trust-row">
      <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v5c0 5-3 8.5-7 10-4-1.5-7-5-7-10V6l7-3Z" stroke="currentColor" stroke-width="1.6"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg> Verified Venues</span>
      <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M9 12.5l2 2 4-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg> Best Price Guarantee</span>
      <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg> 24/7 Support</span>
      <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="5" y="10.5" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.6"/></svg> Easy &amp; Secure Booking</span>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Popular Event Categories</h2>
        <p>Discover venues for every special moment</p>
      </div>
      <a href="#venues" class="btn btn-outline btn-small">View All Venues</a>
    </div>

    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <?php
          $catImg = __DIR__ . '/images/venues/' . $cat['img'] . '.jpg';
          $catImgUrl = 'images/venues/' . $cat['img'] . '.jpg?v=' . (file_exists($catImg) ? filemtime($catImg) : 1);
        ?>
        <a class="category-card" href="index.php?type=<?php echo urlencode($cat['type']); ?>#venues" style="background-image:url('<?php echo htmlspecialchars($catImgUrl); ?>')">
          <div class="category-card-label">
            <h3><?php echo htmlspecialchars($cat['title']); ?></h3>
            <span><?php echo htmlspecialchars($cat['sub']); ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="venues" style="padding-top:0;">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Available Venues</h2>
        <p><?php echo mysqli_num_rows($result); ?> venue(s) match your search</p>
      </div>
    </div>

    <div class="grid">
      <?php if (mysqli_num_rows($result) === 0): ?>
        <p>No venues match those filters yet — try widening your search.</p>
      <?php endif; ?>
      <?php while ($v = mysqli_fetch_assoc($result)): ?>
        <?php
          $imgPath = __DIR__ . '/images/venues/' . $v['image_seed'] . '.jpg';
          $hasImg  = $v['image_seed'] && file_exists($imgPath);
          $imgFile = $hasImg ? 'images/venues/' . rawurlencode($v['image_seed']) . '.jpg?v=' . filemtime($imgPath) : '';
        ?>
        <a class="card" href="venue_details.php?id=<?php echo $v['venue_id']; ?>">
          <div class="card-media" data-type="<?php echo htmlspecialchars($v['venue_type']); ?>"<?php echo $hasImg ? ' style="background-image:url(\'' . htmlspecialchars($imgFile) . '\')"' : ''; ?>></div>
          <div class="card-body">
            <h3><?php echo htmlspecialchars($v['venue_name']); ?></h3>
            <div class="card-meta">
              <span>📍 <?php echo htmlspecialchars($v['location']); ?></span>
              <span>👥 Up to <?php echo (int) $v['capacity']; ?></span>
            </div>
            <div class="card-price">
              <strong>₹<?php echo number_format($v['price_per_hour']); ?>/hr</strong>
              <span class="status-tag status-<?php echo $v['availability'] === 'Available' ? 'Available' : 'Booked'; ?>">
                <?php echo htmlspecialchars($v['availability']); ?>
              </span>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<section class="section steps-band" id="how-it-works">
  <div class="container">
    <div class="section-intro">
      <h2>How It Works</h2>
      <p>Booking your dream venue is just a few clicks away</p>
    </div>
    <div class="steps-grid">
      <div class="step">
        <div class="step-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <h3>1. Search</h3>
        <p>Enter your location, date and guest count</p>
        <span class="step-arrow"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      </div>
      <div class="step">
        <div class="step-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <h3>2. Compare</h3>
        <p>Explore and compare top venues</p>
        <span class="step-arrow"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      </div>
      <div class="step">
        <div class="step-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M4 9.5h16M8 3v3.5M16 3v3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <h3>3. Book</h3>
        <p>Choose your venue and confirm instantly</p>
        <span class="step-arrow"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      </div>
      <div class="step">
        <div class="step-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M4 20l4-1 9-9-3-3-9 9-1 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 5l2 2M17 3l2 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></div>
        <h3>4. Celebrate</h3>
        <p>Focus on your event, we've got the rest!</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-intro">
      <h2>What Our Users Say</h2>
      <p>Real experiences. Happier celebrations.</p>
    </div>
    <div class="testimonial-grid">
      <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <div class="testimonial-stars"><?php echo str_repeat('★', max(1, min(5, $t['rating']))); ?></div>
          <p class="testimonial-quote">&ldquo;<?php echo htmlspecialchars($t['quote']); ?>&rdquo;</p>
          <div class="testimonial-person">
            <span class="avatar-circle"><?php echo strtoupper(substr($t['name'], 0, 1)); ?></span>
            <strong>&mdash; <?php echo htmlspecialchars($t['name']); ?></strong>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="cta-band-media" style="background-image:url('images/venues/pavilion.jpg?v=<?php echo filemtime(__DIR__ . '/images/venues/pavilion.jpg'); ?>');"></div>
  <div class="cta-band-overlay"></div>
  <div class="container cta-band-content">
    <h2>Ready to Create Unforgettable Moments?</h2>
    <p>Find and book the perfect venue today.</p>
    <a href="#venues" class="btn btn-brass">Explore Venues →</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
