<?php
session_start();
include('../includes/db.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            if ($role === 'doctor' || $role === 'user') {
                $_SESSION['user_id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;

                if ($role === 'doctor') {
                    header("Location: ../doctor/dashboard.php");
                    exit;
                } elseif ($role === 'user') {
                    header("Location: ../user/dashboard.php");
                    exit;
                }
            } else {
                $message = "Access denied for this role.";
            }
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title data-i18n="page_title">User Login | TeleMedicine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to bottom right, rgba(255,255,255,0.8), rgba(255,255,255,0.9)), url('../img/sss.jpg') center center/cover no-repeat;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar */
    .navbar {
      background: #3498db;
      color: white;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      box-sizing: border-box;
    }

    .navbar .logo {
      font-size: 24px;
      font-weight: bold;
    }

    .navbar ul {
      list-style: none;
      display: flex;
      gap: 40px;
      margin: 0;
      padding: 0;
      align-items: center;
    }

    .navbar ul li {
      display: flex;
      align-items: center;
    }

    .navbar ul li a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: color 0.3s ease;
    }

    .navbar ul li a:hover {
      color: #dff6ff;
      text-decoration: underline;
    }

    /* Select styling */
    #languageSwitcher {
      padding: 6px 10px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      font-size: 1rem;
    }

    .container {
      flex: 1;
      max-width: 900px;
      height: 730px;
      margin: 80px auto 0 auto; /* give top space for fixed navbar */
      display: flex;
      box-shadow: 0 12px 30px rgba(0,0,0,0.1);
      border-radius: 12px;
      overflow: hidden;
      background: white;
    }

    .welcome-section {
      flex: 1;
      background: url('../img/r.jpg') center center/cover no-repeat;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 60px 40px;
      position: relative;
    }

    .welcome-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: rgba(52, 152, 219, 0.75);
      z-index: 0;
    }

    .welcome-section > * {
      position: relative;
      z-index: 1;
    }

    .welcome-section h1 {
      font-size: 36px;
      margin-bottom: 20px;
      font-weight: 800;
    }

    .welcome-section p {
      font-size: 18px;
      line-height: 1.4;
    }

    .form-section {
      flex: 1;
      padding: 40px 35px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-section h2 {
      font-weight: 700;
      font-size: 28px;
      margin-bottom: 25px;
      color: #333;
      text-align: center;
    }

    form input[type="email"],
    form input[type="password"] {
      width: 100%;
      padding: 12px 14px;
      margin-bottom: 15px;
      border: 1.8px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper i {
      position: absolute;
      right: 10px;
      top: 12px;
      cursor: pointer;
      color: #999;
    }

    .action-buttons {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
      margin-top: 10px;
    }

    button {
      background: linear-gradient(135deg, #3498db, #2c80c2);
      color: white;
      border: none;
      font-weight: 700;
      font-size: 16px;
      padding: 14px 28px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    button:hover {
      background: linear-gradient(135deg, #2c80c2, #1e6fa7);
      transform: translateY(-2px);
    }

    .action-buttons a {
      font-size: 15px;
      color: #3498db;
      font-weight: 600;
      text-decoration: none;
    }

    .action-buttons a:hover {
      text-decoration: underline;
    }

    .message-box {
      padding: 12px 15px;
      margin-bottom: 20px;
      border-left: 5px solid #3498db;
      background: #e6f0fb;
      color: #20518f;
      font-size: 15px;
      border-radius: 5px;
    }

    footer {
      background-color: #2c3e50;
      color: white;
      text-align: center;
      padding: 15px;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        max-width: 400px;
        height: auto;
      }
    }
  </style>
</head>
<body>

<header class="navbar">
  <h1 class="logo" data-i18n="logo">💊 TeleMedicine</h1>
  <nav>
    <ul>
      <li><a href="../index.php" data-i18n="nav_home"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="../index.php#services" data-i18n="nav_services"><i class="fas fa-cogs"></i> Services</a></li>
      <li><a href="../index.php#about" data-i18n="nav_about"><i class="fas fa-info-circle"></i> About</a></li>  
      <li><a href="register.php" data-i18n="nav_registerL"><i class="fas fa-user-plus"></i> Register</a></li>
      <li>
        <select id="languageSwitcher" aria-label="Language Switcher">
          <option value="en">🌐 English</option>
          <option value="am">🇪🇹 አማርኛ</option>
        </select>
      </li>
    </ul>
  </nav>
</header>

<div class="container">
  <section class="welcome-section">
    <h1 data-i18n="login_heading"><i class="fas fa-sign-in-alt"></i> Login</h1><br><br><br><br><br><br><br><br><br><br>
    <h1 data-i18n="welcome_title">Welcome Back!</h1>
    <p data-i18n="welcome_text">Access your TeleMedicine account to consult doctors and manage your medications with ease.</p>
  </section>

  <section class="form-section">
    <h2 data-i18n="user_login">User Login</h2>

    <?php if (!empty($message)): ?>
      <div class="message-box"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="email" name="email" placeholder="" required data-i18n-placeholder="placeholder_email" />
      <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="" required data-i18n-placeholder="placeholder_password" />
        <i class="fas fa-eye" onclick="togglePassword(this)"></i>
      </div>
      <div class="action-buttons">
        <button type="submit" data-i18n="btn_login">Login</button>
        <a href="register.php" data-i18n="register_link">Don't have an account? Register</a>
      </div>
    </form>
  </section>
</div>

<footer data-i18n="footer_textL">
  &copy; 2025 TeleMedicine. All rights reserved.
</footer>

<script>
function togglePassword(icon) {
  const input = icon.previousElementSibling;
  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = "password";
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

const languageSwitcher = {
  currentLang: localStorage.getItem('siteLang') || 'en',
  translations: {},
  jsonPath: '../assets/lang.json',

  fetchTranslations: async function() {
    try {
      const response = await fetch(this.jsonPath);
      if (!response.ok) throw new Error('Failed to load language file');
      this.translations = await response.json();
      this.applyTranslations();
      document.getElementById('languageSwitcher').value = this.currentLang;
    } catch (error) {
      console.error(error);
    }
  },

  applyTranslations: function() {
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (this.translations[this.currentLang] && this.translations[this.currentLang][key]) {
        el.innerHTML = this.translations[this.currentLang][key];
      }
    });

    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
      const key = el.getAttribute('data-i18n-placeholder');
      if (this.translations[this.currentLang] && this.translations[this.currentLang][key]) {
        el.placeholder = this.translations[this.currentLang][key];
      }
    });

    document.querySelectorAll('[data-i18n-title]').forEach(el => {
      const key = el.getAttribute('data-i18n-title');
      if (this.translations[this.currentLang] && this.translations[this.currentLang][key]) {
        el.title = this.translations[this.currentLang][key];
      }
    });
  },

  changeLanguage: function(lang) {
    this.currentLang = lang;
    localStorage.setItem('siteLang', lang);
    this.applyTranslations();
  },

  init: function() {
    this.fetchTranslations();
    document.getElementById('languageSwitcher').addEventListener('change', (e) => {
      this.changeLanguage(e.target.value);
    });
  }
};

languageSwitcher.init();
</script>

</body>
</html>
