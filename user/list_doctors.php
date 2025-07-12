<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

// Handle search input safely
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Doctors - TeleMedicine</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />

    <style>
        :root {
            --primary:#27ae60;
            --white: #fff;
            
        }
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f8fa;
    margin: 0; padding: 0;
  }
        /* Container and layout */
        main.content-wrapper {
            position: absolute;
            top: 60px;
            left: 10px; /* Assuming your sidebar width is 300px */
            right: 0;
            bottom: 50px;
            overflow: auto;
            background-color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
            padding: 20px 40px;
            box-sizing: border-box;
        }

        .main-content h1 {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
        }

        /* Grid for doctor cards */
        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        /* Doctor card style */
        .doctor-card {
            background: var(--white);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .doctor-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 2px solid var(--primary);
        }

        .doctor-card h3 {
            color: var(--primary);
            margin: 0 0 10px;
        }

        .doctor-card p {
            margin: 5px 0;
            color: #333;
            font-size: 0.95rem;
        }

        .doctor-card .email,
        .doctor-card .phone {
            font-size: 0.9rem;
            color: #666;
        }

        .view-button {
            margin-top: 15px;
            padding: 8px 16px;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .view-button:hover {
            background-color: #219150;
        }

        /* Search form styling */
        .search-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .search-form input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 250px;
            font-size: 1rem;
        }

        .search-form button {
            padding: 8px 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .search-form button:hover {
            background-color: #219150;
        }
        
    </style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>

<main class="content-wrapper">
    <div class="main-content">
        <h1>Available Doctors</h1>

        <!-- Search form -->
        <form method="GET" class="search-form" role="search" aria-label="Search doctors">
            <input
                type="text"
                name="search"
                placeholder="Search by name or specialization..."
                value="<?php echo htmlspecialchars($search); ?>"
                aria-label="Search doctors by name or specialization"
            />
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>

        <div class="doctor-grid">
            <?php
            $query = "
                SELECT u.id, u.name, u.email, u.phone, u.photo, d.specialty, d.experience, d.consult_fee,
                       AVG(dr.rating) AS avg_rating,
                       COUNT(dr.rating) AS rating_count
                FROM users u
                INNER JOIN doctor d ON u.id = d.user_id
                LEFT JOIN doctor_ratings dr ON u.id = dr.doctor_id
                WHERE u.role = 'doctor'
            ";

            if (!empty($search)) {
                $query .= " AND (u.name LIKE '%$search%' OR d.specialty LIKE '%$search%')";
            }

            $query .= " GROUP BY u.id";

            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "<p>Error fetching doctors: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
            } elseif (mysqli_num_rows($result) === 0) {
                echo "<p>No doctors found matching your search.</p>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $photoPath = !empty($row['photo']) && file_exists('../uploads/profile_photos/' . $row['photo'])
                        ? '../uploads/profile_photos/' . htmlspecialchars($row['photo'])
                        : '../uploads/default_avatar.png';

                    $avgRating = round(floatval($row['avg_rating']), 1);
                    $ratingCount = intval($row['rating_count']);

                    echo '<div class="doctor-card">';
                    echo '<img src="' . $photoPath . '" alt="Photo of Dr. ' . htmlspecialchars($row['name']) . '" class="doctor-photo" />';
                    echo '<h3><i class="fas fa-user-md"></i> Dr. ' . htmlspecialchars($row['name']) . '</h3>';

                    // Display star rating
                    echo '<p aria-label="Average rating: ' . $avgRating . ' out of 5 stars">';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($avgRating)) {
                            echo '<i class="fas fa-star" style="color: #f39c12;"></i>'; // full star
                        } elseif ($i - 0.5 <= $avgRating) {
                            echo '<i class="fas fa-star-half-alt" style="color: #f39c12;"></i>'; // half star
                        } else {
                            echo '<i class="far fa-star" style="color: #ccc;"></i>'; // empty star
                        }
                    }
                    echo ' <small>(' . $ratingCount . ')</small>';
                    echo '</p>';

                    echo '<p class="email"><i class="fas fa-envelope"></i> ' . htmlspecialchars($row['email']) . '</p>';
                    echo '<p class="phone"><i class="fas fa-phone"></i> ' . htmlspecialchars($row['phone']) . '</p>';
                    echo '<p><strong>Specialization:</strong> ' . htmlspecialchars($row['specialty']) . '</p>';
                    echo '<p><strong>Experience:</strong> ' . htmlspecialchars($row['experience']) . ' years</p>';
                    echo '<p><strong>Consultation Fee:</strong> $' . htmlspecialchars($row['consult_fee']) . '</p>';
                    echo '<a href="view_doctor.php?id=' . urlencode($row['id']) . '" class="view-button">View</a>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
