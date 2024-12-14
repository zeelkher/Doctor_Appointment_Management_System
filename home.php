<?php
include_once('db.php');

// Fetching doctor details from the database
$query = "SELECT Doctor_FirstName, Doctor_LastName, Doctor_Address, Doctor_specialities, Doctor_Phone, Doctor_Email FROM doctor LIMIT 5";
$result = mysqli_query($conn, $query);

$doctors = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }
} else {
    echo "Error fetching doctor details: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <title>Doctor Appointment Booking</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: #333;
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }

        /* Sticky Header */
        .header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #1e3a8a;
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .header a {
            background-color: #f9fafb;
            color: #1e3a8a;
            text-decoration: none;
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        .header a:hover {
            background-color: #f3f4f6;
        }

        .main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .hero {
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h2 {
            font-size: 2rem;
            color: #1e3a8a;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1rem;
            color: #4b5563;
        }

        .cta {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .cta a {
            background-color: #1e3a8a;
            color: #fff;
            text-decoration: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        .cta a:hover {
            background-color: #374151;
        }

        .doctors {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 1.2rem;
            color: #1e3a8a;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 0.9rem;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .hero h2 {
                font-size: 1.5rem;
            }

            .cta a {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Health Care System</h1>
        <a href="drlogin.php">Doctor Login</a>
    </div>

    <div class="main">
        <section class="hero">
            <h2>Book Your Appointment Now</h2>
            <p>Log-In for Book Appointment.</p>
            <p>Get expert care and consultation from doctors.</p>
            <div class="cta">
                <a href="login.php">Log In</a>
                <a href="registration.php">Register</a>
            </div>
        </section>

        <section class="doctors">
            <?php if (!empty($doctors)): ?>
                <?php foreach ($doctors as $doctor): ?>
                    <div class="card">
                        <img src="img/dr.jpeg" alt="Doctor">
                        <h3><?php echo htmlspecialchars($doctor['Doctor_FirstName'] . ' ' . $doctor['Doctor_LastName']); ?></h3>
                        <p><strong>Speciality:</strong> <?php echo htmlspecialchars($doctor['Doctor_specialities']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($doctor['Doctor_Address']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['Doctor_Email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($doctor['Doctor_Phone']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <h3>No Doctors Available</h3>
                    <p>Check back later for updates.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="footer">
        &copy; 2024 Health Care System. All rights reserved.
    </div>
</body>
</html>
