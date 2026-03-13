<?php
session_start();
include 'config.php';

// registered donors
$donor_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='donor'")
->fetch_assoc()['total'];

// total donations
$donation_count = $conn->query("SELECT COUNT(*) as total FROM screenings")
->fetch_assoc()['total'];

// lives impacted
$lives_impacted = $donation_count * 3;

// active donors
$active_donors = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='donor' AND status='active'")
->fetch_assoc()['total'];



// donations this month
$donations_month = $conn->query("
SELECT COUNT(*) as total 
FROM screenings 
WHERE MONTH(date)=MONTH(CURRENT_DATE()) 
AND YEAR(date)=YEAR(CURRENT_DATE())
")->fetch_assoc()['total'];

// donations this year
$donations_year = $conn->query("
SELECT COUNT(*) as total 
FROM screenings 
WHERE YEAR(date)=YEAR(CURRENT_DATE())
")->fetch_assoc()['total'];

$lives_saved_year = $donations_year * 3;

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Red Trace | Mapúa University Blood Donation</title>
    <link rel="stylesheet" href="style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
  </head>

  <body class="home-page">
    <header id="main-header">
      <nav class="navbar">
        <div class="logo">
          <a
            href="index.php"
            style="
              text-decoration: none;
              display: flex;
              align-items: center;
              gap: 12px;
              color: inherit;
            "
          >
            <img src="mapua-logo.png" alt="Mapúa Logo" />
            <span>Mapúa RedTrace</span>
          </a>
        </div>
       <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="help.php">Help/Policies</a></li>

            <?php if(isset($_SESSION['user_id'])){ ?>
              <li><a href="notifications.php">Notifications</a></li>
            <?php } ?>

          </ul>
        <div class="nav-actions">
              <?php if(isset($_SESSION['user_id'])) { ?>

              <span style="color:white;font-weight:600;">
              Welcome, <?php echo $_SESSION['name']; ?>
              </span>

              <?php } else { ?>

              <a href="login.html" class="btn btn-red" style="text-decoration:none;">
              Sign-In/Register
              </a>

              <?php } ?>
          <div class="user-icon"><i class="fa-regular fa-user"></i></div>
        </div>
      </nav>
    </header>

    <section class="hero">
      <div class="hero-content">
        <span class="badge"
          ><i class="fa-solid fa-heart-pulse"></i> University Blood Donation
          Program</span
        >
        <h1>Save Lives with <span>RedTrace</span></h1>
        <p>
          A comprehensive blood donation management system designed to
          streamline donor registration, track donations, and coordinate
          life-saving efforts across our university community.
        </p>
        <div class="hero-btns">
          <a href="donation-process.php" style="text-decoration: none">
            <button class="btn btn-red">
              <i class="fa-solid fa-heart"></i> Donate Now
            </button>
          </a>
          <button class="btn btn-white" id="main-dashboard-btn">
            <i class="fa-solid fa-table-columns"></i> View Dashboard
          </button>
        </div>
      </div>

      <div class="summary-stats">
        <div class="stat-box">
          <h2><?php echo $donor_count; ?></h2>
          <p>Registered Donors</p>
        </div>

        <div class="stat-box">
          <h2><?php echo $donation_count; ?></h2>
          <p>Total Donations</p>
        </div>

        <div class="stat-box">
          <h2><?php echo $lives_impacted; ?></h2>
          <p>Lives Impacted</p>
        </div>
      </div>
    </section>

    <section class="impact">
      <div class="container">
        <h2>Our Impact</h2>
        <p class="subtitle">
          Together, we're making a difference in our community through
          consistent blood donations and dedicated support.
        </p>

        <div class="impact-grid">
          <div class="impact-card">
            <div class="icon-circle pink-bg">
              <i class="fa-solid fa-user-plus"></i>
            </div>
            <h3><?php echo $active_donors; ?></h3>
            <p>Active Donors</p>
            <span class="trend positive">+12.5% <span>this month</span></span>
          </div>
         
          <div class="impact-card">
            <div class="icon-circle pink-bg">
              <i class="fa-solid fa-droplet"></i>
            </div>
            <h3><?php echo $donations_month; ?></h3>
            <p>Donations This Month</p>>
            <span class="trend positive">+8.3% <span>vs last month</span></span>
          </div>
          <div class="impact-card">
            <div class="icon-circle pink-bg">
              <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <h3><?php echo $lives_saved_year; ?></h3>
            <p>Lives Saved This Year</p>  
            <span class="trend">Each donation saves 3 lives</span>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section">
      <div class="cta-banner">
        <h2>Ready to Make a Difference?</h2>
        <p>
          Join our community of life-savers today. Every donation counts, and
          your contribution can save up to three lives. Register now and become
          a hero in someone's story.
        </p>

        <div class="cta-btns">
          <button class="btn btn-white">
            <i class="fa-solid fa-user-plus"></i> Donate Now
          </button>
          <button class="btn btn-outline">
            <i class="fa-solid fa-circle-info"></i> Learn More
          </button>
        </div>

        <div class="cta-features">
          <div class="feature-item">
            <strong>24/7</strong>
            <p>Support Available</p>
          </div>
          <div class="feature-item">
            <strong>&lt;5 min</strong>
            <p>Registration Time</p>
          </div>
          <div class="feature-item">
            <strong>100%</strong>
            <p>Secure & Private</p>
          </div>
        </div>
      </div>
    </section>

    <footer>
      <div class="footer-container">
        <div class="footer-brand">
          <div class="logo">
            <img src="mapua-logo.png" alt="Mapúa Logo" />
            <span>RedTrace</span>
          </div>
          <p>
            Empowering our university community to save lives through efficient
            blood donation management and transparent tracking systems.
          </p>
          <div class="social-icons">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
        </div>
        <div class="footer-links">
          <h4>Quick Links</h4>
          <a href="index.php">Home</a>
          <a href="notifications.php">Notifications</a>
          <a href="help.php">Help/Policies</a>
        </div>
        <div class="footer-contact">
          <h4>Contact Us</h4>
          <p>
            <i class="fa-solid fa-location-dot"></i> University Medical Center
            Room 201, Main Campus
          </p>
          <p><i class="fa-solid fa-phone"></i> +1 (555) 123-4567</p>
          <p><i class="fa-solid fa-envelope"></i> info@redtrace.edu</p>
          <p><i class="fa-solid fa-clock"></i> Mon-Fri: 9AM - 5PM</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 RedTrace. All rights reserved.</p>
      </div>
    </footer>

    <script src="script.js"></script>
  </body>
</html>
