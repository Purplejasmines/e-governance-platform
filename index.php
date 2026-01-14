<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Governance Portal</title>
  <link rel="stylesheet" href="assets/css/landing.css">
  <!-- AOS CSS -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

</head>
<body>
  <header class="hero-section">
    <div class="hero-content">
      <h1>Welcome to the E-Governance Portal</h1>
      <p>Your digital gateway to public services and essential documents.</p>
      <a href="Users\register.php" class="cta-button">Get Started</a>
    </div>
  </header>

  <!-- How it Works Section -->
  <section class="how-it-works">
  <h2 data-aos="fade-down">How It Works</h2>
  <div class="card-container">
    <div class="how-card" data-aos="fade-up" data-aos-delay="100">
      <img src="assets\register.svg" alt="Register Illustration">
      <h3>Create an Account</h3>
      <p>Sign up or log in to access digital services.</p>
    </div>

    <div class="how-card" data-aos="fade-up" data-aos-delay="200">
      <img src="assets\form.svg" alt="Submit Request Illustration">
      <h3>Apply for a Service</h3>
      <p>Choose your service and fill out a simple form.</p>
    </div>

    <div class="how-card" data-aos="fade-up" data-aos-delay="300">
      <img src="assets\progress.svg" alt="Track Progress Illustration">
      <h3>Track Request</h3>
      <p>Monitor your request and receive updates in real-time.</p>
    </div>

    <div class="how-card" data-aos="fade-up" data-aos-delay="400">
      <img src="assets\certificate.svg" alt="Download Document Illustration">
      <h3>Download Certificate</h3>
      <p>Once approved, download your permit or certificate instantly.</p>
    </div>
  </div>
</section>

    <!-- Features Section -->
    <section class="features" id="features">
  <h2 data-aos="fade-down">Platform Features</h2>
  <div class="features-grid">

    <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
      <img src="assets\mobile.svg" alt="Digital Access">
      <h3>Digital Access</h3>
      <p>Use the platform anywhere, at any time, from any device.</p>
    </div>

    <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
      <img src="assets\progress-indicator.svg" alt="Track Updates">
      <h3>Real-Time Tracking</h3>
      <p>Get notified and follow the progress of your applications easily.</p>
    </div>

    <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
      <img src="assets\fast.svg" alt="Fast Approvals">
      <h3>Quick Approvals</h3>
      <p>Receive your documents faster with an optimized review process.</p>
    </div>

    <div class="feature-card" data-aos="zoom-in" data-aos-delay="400">
      <img src="assets\secure.svg" alt="Security">
      <h3>Secure & Trusted</h3>
      <p>Your information is encrypted and handled with care.</p>
    </div>
  </div>
</section>

<!--About Section -->
<section class="about-platform" id="about">
  <div class="about-container" data-aos="fade-up">
    <h2>About the Platform</h2>
    <p>
      The E-Governance Portal is designed to simplify how citizens interact with government services. 
      Whether you're applying for a permit, requesting a certificate, or tracking your application status, 
      our goal is to make the process smooth, transparent, and accessible — all from the comfort of your device.
    </p>
  </div>
</section>

<!-- Wave Divider -->
<div class="wave-divider">
  <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
    <path fill="#f1f1f1" d="M0,64L48,58.7C96,53,192,43,288,58.7C384,75,480,117,576,128C672,139,768,117,864,101.3C960,85,1056,75,1152,85.3C1248,96,1344,128,1392,144L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
  </svg>
</div>

<section class="faqs" id="faqs">
  <h2 data-aos="fade-up">Frequently Asked Questions</h2>
  <div class="faq-container">
    <div class="faq-item">
      <button class="faq-question">How do I apply for a certificate?</button>
      <div class="faq-answer">
        <p>Once logged in, go to “Services”, choose “Certificate”, select the category, and fill out the form. You’ll receive updates as it’s processed.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Can I track the status of my request?</button>
      <div class="faq-answer">
        <p>Yes! Go to your dashboard and check the status under “My Requests”. Updates are shown in real time.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Is my information secure?</button>
      <div class="faq-answer">
        <p>Absolutely. We use encrypted storage and secure login to keep your data private and protected.</p>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="main-footer">
  <div class="footer-content">
    <p>&copy; <?php echo date("Y"); ?> E-Governance Portal. All rights reserved.</p>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Contact Support</a>
    </div>
  </div>
</footer>



  <!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
  });
</script>

<script>
  // FAQ Toggle
  document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
      const answer = button.nextElementSibling;
      const isVisible = answer.style.display === 'block';

      document.querySelectorAll('.faq-answer').forEach(ans => ans.style.display = 'none');

      answer.style.display = isVisible ? 'none' : 'block';
    });
  });
</script>


</body>
</html>
