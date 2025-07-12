<?php
include 'includes/db.php';
$userCount = $doctorCount = $appointmentCount = 0;
if ($conn) {
    $res = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='user'");
    if ($res) { $row = mysqli_fetch_assoc($res); $userCount = $row['total'] ?? 0; }
    $res = mysqli_query($conn,"SELECT COUNT(*) AS total FROM doctor");
    if ($res) { $row = mysqli_fetch_assoc($res); $doctorCount = $row['total'] ?? 0; }
    $res = mysqli_query($conn,"SELECT COUNT(*) AS total FROM appointments");
    if ($res) { $row = mysqli_fetch_assoc($res); $appointmentCount = $row['total'] ?? 0; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
 <title data-i18n="page_titleHAA">TeleMedicine | Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"/>
  <style>
    html { scroll-behavior: smooth; }
    body { margin:0; font-family:'Segoe UI',sans-serif; background:#f4f7f9; }
    .navbar {
      background:#3498db; color:#fff; position:fixed; top:0; width:100%;
      padding:15px 40px; box-shadow:0 2px 8px rgba(0,0,0,0.15);
      display:flex; justify-content:space-between; align-items:center; z-index:1000;
    }
    .logo { font-size:24px; font-weight:bold; cursor:pointer; }
    .navbar ul { list-style:none; display:flex; gap:40px; margin:0; padding:0; align-items:center; }
    .navbar a {
      color:#fff; text-decoration:none; font-weight:500;
      display:flex; gap:6px; transition:color .3s;
    }
    .navbar a:hover { color:#d4e6f1; }
    .hero {
      padding:120px 20px 80px; text-align:center;
      background:linear-gradient(135deg,#3498db,#2ecc71);
      color:#fff;
      user-select:none;
    }
    .hero h1 {
      font-size:3rem; margin-bottom:.4rem;
      animation: fadeInUp 1s ease forwards;
    }
    .hero p {
      font-size:1.2rem; opacity:.9;
      animation: fadeInUp 1.3s ease forwards;
    }
    .hero .btn {
      padding:12px 28px; background:#fff; color:#3498db;
      border-radius:6px; font-weight:700;
      box-shadow:0 6px 12px rgba(52,152,219,0.3);
      transition:.3s; cursor:pointer;
      animation: fadeInUp 1.6s ease forwards;
      border:none;
    }
    .hero .btn:hover {
      background:#2980b9; color:#fff;
    }

    @keyframes fadeInUp {
      0% {opacity: 0; transform: translateY(20px);}
      100% {opacity: 1; transform: translateY(0);}
    }

    .slider-container {
      max-width:900px; margin:2rem auto; overflow:hidden;
      border-radius:15px; box-shadow:0 6px 18px rgba(0,0,0,0.2);
      position:relative;
    }
    .slider {
      display:flex; transition:transform .5s ease;
    }
    .slide {
      min-width:100%; position:relative;
    }
    .slide img {
      width:100%; height:400px; object-fit:cover; display:block;
      border-radius: 15px;
      user-select:none;
    }
    .caption {
      position:absolute; bottom:30px; left:30px;
      background:rgba(0,0,0,0.6); padding:20px; color:#fff;
      border-radius:10px;
      max-width: 80%;
      user-select:none;
    }
    .services, .about-section, .faq, .stats {
      padding:4rem 2rem; background:#fff; text-align:center;
      user-select:none;
    }
    .services h3 {
      font-size:2.2rem; margin-bottom:2.5rem; color:#2c3e50;
    }
    .service-boxes {
      display:grid; grid-template-columns:repeat(3,1fr);
      gap:2rem; max-width:1200px; margin:auto;
    }
    .service {
      background:linear-gradient(135deg,#f0f4f8,#d9e2ec);
      padding:2rem; border-radius:12px;
      box-shadow:0 10px 15px rgba(52,152,219,0.1);
      transition:.3s;
      user-select:none;
    }
    .service:hover {
      transform:translateY(-10px);
      box-shadow:0 15px 25px rgba(52,152,219,0.3);
    }
    .service i {
      font-size:2.8rem; color:#2980b9; margin-bottom:1rem;
    }
    .service h4 {
      font-size:1.3rem; color:#1c2833; margin-bottom:.5rem;
    }
    .stats {
      display:flex; justify-content:center;
      gap:2.5rem; flex-wrap:wrap;
      max-width:1000px; margin:3rem auto;
      border-radius:15px;
      box-shadow:0 8px 30px rgba(0,0,0,0.1);
      user-select:none;
    }
    .stat-card {
      flex:1 1 200px;
      background:linear-gradient(135deg,#74b9ff,#0984e3);
      color:#fff; padding:2rem; border-radius:15px;
      text-align:center;
      user-select:none;
    }
    .stat-icon {
      font-size:3.5rem; margin-bottom:.5rem;
    }
    .stat-number {
      font-size:3rem; font-weight:700;
    }
    .stat-label {
      font-size:1.1rem; font-weight:600;
    }
    .about-container {
      max-width:1000px; margin:auto;
      padding:40px; border-radius:10px; background:#fff;
      user-select:none;
    }
    .team-members {
      display:flex; gap:20px; justify-content:center; flex-wrap:wrap;
    }
    .team-member img {
      border-radius:50%; width:80px; height:80px;
      object-fit:cover; margin-bottom:10px;
      user-select:none;
    }
    details.faq-item {
      max-width:600px; margin:0.5rem auto; padding:1rem;
      background:#f9f9f9; border-radius:8px;
      user-select:none;
    }
    .footer {
      background:#2c3e50; color:#fff;
      text-align:center; padding:20px; margin-top:40px;
      user-select:none;
    }
    #scrollTopBtn {
      position:fixed; bottom:30px; right:30px;
      z-index:999; font-size:1.3rem; border:none;
      background:#3498db; color:#fff; padding:12px;
      border-radius:50%; display:none;
      box-shadow:0 8px 16px rgba(0,0,0,0.3);
      cursor:pointer;
      user-select:none;
      transition: background-color 0.3s ease;
    }
    #scrollTopBtn:hover {
      background:#2980b9;
    }
    #languageSwitcher {
    padding: 6px 10px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    font-size: 1rem;
  }

    @media(max-width:768px){
      .service-boxes, .stats, .team-members { flex-direction:column; }
      .service-boxes { grid-template-columns: 1fr !important; }
      .navbar ul { flex-wrap: wrap; gap:15px; }
    }
  </style>
</head>
<body onload="AOS.init();">

<header class="navbar" id="top">
  <div class="nav-left">
    <div class="logo" data-i18n="logo">💊 TeleMedicine</div>
  </div>
  <nav>
    <ul>
      <li><a href="#top"><i class="fas fa-home"></i> <span data-i18n="home">Home</span></a></li>
      <li><a href="#services"><i class="fas fa-cogs"></i> <span data-i18n="services">Services</span></a></li>
      <li><a href="#about"><i class="fas fa-info-circle"></i> <span data-i18n="about">About</span></a></li>
      <li><a href="#faq"><i class="fas fa-question-circle"></i> <span data-i18n="faq">FAQ</span></a></li>
      <li><a href="auth/register.php"><i class="fas fa-user-plus"></i> <span data-i18n="join">Join</span></a></li>
      <li>
        <select id="languageSwitcher" class="language-switcher" aria-label="Select Language">
          <option value="en">🌐 English</option>
          <option value="am">🇪🇹 አማርኛ</option>
        </select>
      </li>
      <li><a href=""><></i></span></a>......</li>
    </ul>
  </nav>
</header>

<section class="hero" id="join" data-aos="fade-up">
  <h1 data-i18n="hero_title">Welcome to TeleMedicine</h1>
  <p data-i18n="hero_subtitle">Your trusted online medication and health consultation platform.</p>
  <button class="btn" data-i18n="hero_btn" onclick="location.href='auth/Register.php'">Get Started</button>
</section>

<section class="slider-container" data-aos="fade-up" style="margin-top:2rem;">
  <div class="slider">
    <div class="slide" style="display:none;">
      <img src="img/1s.png" alt="" />
      <div class="caption">
        <h4 data-i18n="slide1_title">Easy Appointment Booking</h4>
        <p data-i18n="slide1_text">Schedule in-person or video consultations in seconds.</p>
      </div>
    </div>
    <div class="slide" style="display:none;">
      <img src="img/3s.png" alt="" />
      <div class="caption">
        <h4 data-i18n="slide2_title">Secure Payments with Chapa</h4>
        <p data-i18n="slide2_text">Pay safely and instantly for all consultations.</p>
      </div>
    </div>
    <div class="slide" style="display:none;">
      <img src="img/2s.png" alt="" />
      <div class="caption">
        <h4 data-i18n="slide3_title">Digital Prescription & Medical History</h4>
        <p data-i18n="slide3_text">Access all your prescriptions and records anytime.</p>
      </div>
    </div>
  </div>
</section>

<section class="services" id="services" data-aos="fade-up">
  <h3 data-i18n="services_title">Our Services</h3>
  <div class="service-boxes">
    <div class="service" data-aos="fade-down" data-aos-delay="100">
      <i class="fas fa-user-md"></i>
      <h4 data-i18n="service1_title">Doctor Consultations</h4>
      <p data-i18n="service1_text">Talk to verified doctors online or in‑person.</p>
    </div>

    <div class="service" data-aos="fade-down" data-aos-delay="200">
      <i class="fas fa-calendar-check"></i>
      <h4 data-i18n="service2_title">Appointment Management</h4>
      <p data-i18n="service2_text">Book, accept, and track appointments.</p>
    </div>

    <div class="service" data-aos="fade-down" data-aos-delay="300">
      <i class="fas fa-file-medical-alt"></i>
      <h4 data-i18n="service3_title">Digital Prescriptions</h4>
      <p data-i18n="service3_text">Download doctor-issued prescriptions.</p>
    </div>

    <div class="service" data-aos="fade-down" data-aos-delay="400">
      <i class="fas fa-user-plus"></i>
      <h4 data-i18n="service4_title">Join as Doctor</h4>
      <p data-i18n="service4_text">Apply to become a verified doctor on our platform.</p>
    </div>

    <div class="service" data-aos="fade-down" data-aos-delay="500">
      <i class="fas fa-credit-card"></i>
      <h4 data-i18n="service5_title">Secure Payments</h4>
      <p data-i18n="service5_text">Make payments securely using the Chapa payment gateway.</p>
    </div>

    <div class="service" data-aos="fade-down" data-aos-delay="600">
      <i class="fas fa-pills"></i>
      <h4 data-i18n="service6_title">Medication Access</h4>
      <p data-i18n="service6_text">Browse and receive medications posted by admins.</p>
    </div>
  </div>
</section>

<section class="stats" data-aos="fade-up">
  <div class="stat-card" data-aos="fade-down" data-aos-delay="100">
    <div class="stat-icon"><i class="fas fa-users"></i></div>
    <div class="stat-number"><?php echo $userCount;?></div>
    <div class="stat-label" data-i18n="stat_users">Users</div>
  </div>
  <div class="stat-card" data-aos="fade-down" data-aos-delay="300">
    <div class="stat-icon"><i class="fas fa-user-md"></i></div>
    <div class="stat-number"><?php echo $doctorCount;?></div>
    <div class="stat-label" data-i18n="stat_doctors">Doctors</div>
  </div>
  <div class="stat-card" data-aos="fade-down" data-aos-delay="500">
    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
    <div class="stat-number"><?php echo $appointmentCount;?></div>
    <div class="stat-label" data-i18n="stat_appointments">Appointments</div>
  </div>
</section>

<section class="about-section" id="about" data-aos="fade-right" data-aos-duration="1000">
  <div class="about-container">
    <h1 data-i18n="about_title">About TeleMedicine</h1>
    <p data-i18n="about_text">
      TeleMedicine is your one-stop platform for online medical consultations, secure payments, digital prescriptions, and medication delivery.
    </p>
    <div class="team-members">
      <div class="team-member" data-aos="fade-left" data-aos-delay="300" >
        <img src="img/3d.jpg" alt="Team member 1" />
        <h4 data-i18n="team1">Dr. Alice Smith|Chief Medical Officer</h4>
      </div>
      <div class="team-member" data-aos="fade-right" data-aos-delay="400" >
        <img src="img/3d.jpg" alt="Team member 2" />
        <h4 data-i18n="team2">Dr. John Doe|Lead Specialist</h4>
      </div>
      <div class="team-member" data-aos="fade-left" data-aos-delay="500" > 
        <img src="img/2d.jpeg" alt="Team member 3" />
        <h4 data-i18n="team3">Dr. Mary Johnson|Senior Consultant</h4>
      </div>
    </div>
  </div>
</section>

<section class="faq" id="faq" data-aos="fade-left" data-aos-duration="1000">
  <h3 data-i18n="faq_title">Frequently Asked Questions</h3>

  <details class="faq-item">
    <summary><strong>How do I register an account?</strong></summary>
    <p>1. Go to the homepage and click the “Join” link or the “Get Started” button.</p>
  
    <p>2. Fill in your personal details including name, age, email, and password.</p>
   
    <p>3. Upload your profile photo, confirm your password, and click “Register”.</p>
    
    <p>4. You’ll be redirected to the login page once registration is successful.</p>
  </details>

  <details class="faq-item">
    <summary><strong>How do I pay the consultation fee?</strong></summary>
    <p>1. After the doctor accepts your appointment, a “Pay Now” button will appear on your appointment page.</p>

    <p>2. Click on “Pay Now” to open the Chapa payment page.</p>
   
    <p>3. Enter your card or mobile money details, then click “Pay”.</p>
   
    <p>4. After successful payment, your appointment will be marked as “Paid”.</p>
  </details>

  <details class="faq-item">
    <summary><strong>How can I update my personal details?</strong></summary>
    <p>1. Login to your account and go to your profile page.</p>
 
    <p>2. Click the “Edit” button beside your information.</p>
  
    <p>3. Change your details like name, city, or phone number and click “Save”.</p>
    
  </details>
</section>


<footer class="footer" data-i18n="footer">
  <p>&copy; 2025 TeleMedicine. All rights reserved.</p>
</footer>

<button id="scrollTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init();

  // Scroll to top button logic
  const scrollTopBtn = document.getElementById("scrollTopBtn");
  window.onscroll = () => {
    if (document.body.scrollTop > 400 || document.documentElement.scrollTop > 400) {
      scrollTopBtn.style.display = "block";
    } else {
      scrollTopBtn.style.display = "none";
    }
  };
  scrollTopBtn.onclick = () => {
    window.scrollTo({top: 0, behavior: 'smooth'});
  };

  // Slider logic
  const slides = document.querySelectorAll('.slide');
  let currentSlide = 0;
  function showSlide(index) {
    slides.forEach((slide,i) => {
      slide.style.display = (i === index) ? 'block' : 'none';
    });
  }
  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }
  showSlide(0);
  setInterval(nextSlide, 4000);

  // Language switching code

  let translations = {};

  // Load language JSON file and apply saved language or default
  async function loadTranslations() {
    try {
      const response = await fetch('assets/lang.json');
      translations = await response.json();
      const savedLang = localStorage.getItem('siteLang') || 'en';
      applyLanguage(savedLang);
      document.getElementById('languageSwitcher').value = savedLang;
    } catch(e) {
      console.error('Error loading lang.json:', e);
    }
  }

  function applyLanguage(lang) {
    if (!translations[lang]) return;
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (translations[lang][key]) {
        el.textContent = translations[lang][key];
      }
    });
    localStorage.setItem('siteLang', lang);
  }

  document.getElementById('languageSwitcher').addEventListener('change', e => {
    applyLanguage(e.target.value);
  });

  loadTranslations();
</script>

</body>
</html>
