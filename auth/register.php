<?php
include('../includes/db.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $email = $_POST['email'] ?? '';
    $raw_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $age = intval($_POST['age'] ?? 0);
    $city = htmlspecialchars($_POST['city'] ?? '');
    $phone = $_POST['phone'] ?? '';

    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = basename($_FILES['photo']['name']);
        $fileType = $_FILES['photo']['type'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = '../uploads/profile_photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $photo_path = $fileName;
            } else {
                $message = "❌ Error uploading photo.";
            }
        } else {
            $message = "❌ Unsupported photo format.";
        }
    }

    if (!$message) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "📧 Invalid email format. Must contain '@' and '.'";
        } elseif (!preg_match('/^\d{10}$/', $phone)) {
            $message = "📱 Phone number must be exactly 10 digits.";
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $raw_password)) {
            $message = "🔐 Password must include uppercase, lowercase and number.";
        } elseif ($raw_password !== $confirm_password) {
            $message = "❌ Passwords do not match.";
        } else {
            $password = password_hash($raw_password, PASSWORD_DEFAULT);
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = "❌ Email already exists.";
            } else {
                if ($photo_path) {
                    $stmt = $conn->prepare("INSERT INTO users (name, gender, email, password, age, city, phone, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssisss", $name, $gender, $email, $password, $age, $city, $phone, $photo_path);
                } else {
                    $stmt = $conn->prepare("INSERT INTO users (name, gender, email, password, age, city, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssiss", $name, $gender, $email, $password, $age, $city, $phone);
                }

                if ($stmt->execute()) {
                    $message = "✅ Registered successfully! <a href='login.php' data-i18n='login_link'>Login here</a>";
                } else {
                    $message = "❌ Error: " . $stmt->error;
                }
            }
        }
    }
}
?>


<!-- HTML Section Starts Below -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title data-i18n="page_title">User Registration | TeleMedicine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* --- All CSS remains same as your original, so I skipped repeating it here --- */
    /* Only add below block for gender spacing: */

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



  body {
    padding-top: 0px;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to bottom right, rgba(255,255,255,0.8), rgba(255,255,255,0.9)), url('../img/sss.jpg') center center/cover no-repeat;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  .container {
    flex: 1;
    display: flex;
    max-width: 900px;
    margin: 40px auto;
    margin-top: 0px;
    margin-bottom: 0px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    background: white;
  }

  .welcome-section {
    flex: 1;
    background: url('../img/r.jpg') center center/cover no-repeat;
    color: white;
    padding: 50px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
  }

  .welcome-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(52, 152, 219, 0.75);
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
    padding-top: 50px;
  }

  form input[type="text"],
  form input[type="email"],
  form input[type="password"],
  form input[type="number"],
  form input[type="file"] {
    width: 100%;
    padding: 12px 14px;
    margin-bottom: 15px;
    border: 1.8px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
  }

  .form-row {
    display: flex;
    gap: 4%;
    margin-bottom: 15px;
  }

  .form-row input {
    width: 48%;
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

  form .action-buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-top: 10px;
  }
form select {
  width: 100%;
  padding: 12px 14px;

  margin-bottom: 15px;
  border: 1.8px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='gray' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0px center;
  background-size: 12px;
}

  form button {
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

  form button:hover {
    background: linear-gradient(135deg, #2c80c2, #1e6fa7);
    transform: translateY(-2px);
  }

  form .action-buttons a {
    font-size: 15px;
    color: #3498db;
    font-weight: 600;
    text-decoration: none;
  }

  form .action-buttons a:hover {
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
    }

    .form-row {
      flex-direction: column;
    }

    .form-row input {
      width: 100%;
    }
  }

    .gender-group {
      display: flex;
      gap: 20px;
      margin-bottom: 15px;
      font-weight: 500;
      color: #333;
    }
    .gender-group label {
      display: flex;
      align-items: center;
      gap: 6px;
    }
  </style>
</head>
<body>

<header class="navbar">
  <h1 class="logo" data-i18n="logo">💊 TeleMedicine</h1>
  <nav>
    <ul>
      <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="../index.php#services"><i class="fas fa-cogs"></i> Services</a></li>
      <li><a href="../index.php#about"><i class="fas fa-info-circle"></i> About</a></li>
      <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
      <li>
        <select id="languageSwitcher">
          <option value="en">🌐 English</option>
          <option value="am">🇪🇹 አማርኛ</option>
        </select>
      </li>
    </ul>
  </nav>
</header>

<div class="container">
  <section class="welcome-section">
    <h1><i class="fas fa-user-plus"></i> Registration</h1><br><br><br><br><br><br><br><br><br><br>
    <h1>Welcome to TeleMedicine!</h1>
    <p>Get connected with the best medical professionals and services.</p>
  </section>

  <section class="form-section">
    <h2>Create an Account</h2>

    <?php if ($message): ?>
      <div class="message-box"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" novalidate>
      <input type="text" name="name" placeholder="Full Name" required />

      <!-- Gender selection -->
      <select name="gender" required>
  <option value="" disabled selected>Select Gender</option>
  <option value="Male">Male</option>
  <option value="Female">Female</option>
 
</select>

      <input type="email" name="email" placeholder="Email Address" required />
      <div class="form-row">
        <input type="number" name="age" placeholder="Age" min="1" max="120" required />
        <input type="text" name="city" placeholder="City" required />
      </div>
      <input type="text" name="phone" placeholder="Phone Number (10 digits)" pattern="\d{10}" required />
      <div class="password-wrapper">
        <input type="password" name="password" placeholder="Password" required title="Must contain uppercase, lowercase, number, 8+ characters" />
        <i class="fas fa-eye" onclick="togglePassword(this)"></i>
      </div>

      <div class="password-wrapper">
         <input type="password" name="confirm_password" placeholder="Confirm Password" required />
         <i class="fas fa-eye" onclick="togglePassword(this)"></i>
      </div>
     
      
      <input type="file" name="photo" accept="image/*" />

      <div class="action-buttons">
        <button type="submit">Register</button>
        <a href="login.php">Already have an account? Login</a>
      </div>
    </form>
  </section>
</div>

<footer>
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

  // Language switcher (if needed)
  const languageSwitcher = {
    currentLang: localStorage.getItem('siteLang') || 'en',
    translations: {},
    jsonPath: '../assets/lang.json',

    fetchTranslations: async function () {
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

    applyTranslations: function () {
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

    changeLanguage: function (lang) {
      this.currentLang = lang;
      localStorage.setItem('siteLang', lang);
      this.applyTranslations();
    },

    init: function () {
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
