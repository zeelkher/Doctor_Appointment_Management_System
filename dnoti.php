<?php
include_once('db.php'); 
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");

// Check if Doctor is logged in
if (!isset($_SESSION['Doctor_Id'])) {
    header('Location: home.php');
    exit();
}

$doctorId = $_SESSION['Doctor_Id'];

// SQL query to fetch the upcoming appointments for the doctor
$query = "
    SELECT a.App_Id, a.appointment_date, a.appointment_time, 
        CONCAT(p.Patient_FirstName, ' ', p.Patient_LastName) AS PatientName,
        p.Patient_Phone, p.Patient_Email, p.Patient_Gender, 
        p.Patient_DOB, p.Patient_Marital_Status
    FROM appointments a
    JOIN patients p ON a.Patient_Id = p.Patient_Id
    WHERE a.status = 'pending'
    AND TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.appointment_date, ' ', a.appointment_time)) BETWEEN 0 AND 24
";

// Prepare the query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Notifications</title>
    <style>
        /* Body */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            color: #333;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2C3E50;
            padding: 18px 30px;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: 700;
        }

        .navbar .nav-links {
            display: flex;
            gap: 30px;
        }

        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 30px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .navbar .nav-links a:hover {
            background-color: #1abc9c;
            transform: scale(1.1);
        }

        .navbar .dropdown {
            position: relative;
        }

        .navbar .dropdown-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            padding: 12px 20px;
            transition: background-color 0.3s ease;
        }

        .navbar .dropdown-btn:hover {
            background-color: #1abc9c;
        }

        .navbar .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            background-color: #34495e;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .navbar .dropdown-content a {
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            font-weight: 400;
        }

        .navbar .dropdown:hover .dropdown-content {
            display: block;
        }

        .navbar .dropdown-content a:hover {
            background-color: #1abc9c;
        }

        .navbar .dropdown-content a:last-child:hover {
            background-color: red;
        }

        /* Content */
        .content {
            margin: 40px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            max-width: 800px;
            width: 100%;
        }

        /* Notification Card */
        .notification-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            padding: 25px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .notification-card:hover {
            transform: translateY(-5px);
        }

        .notification-card h2 {
            font-size: 24px;
            color: #1abc9c;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .notification-card p {
            font-size: 16px;
            font-weight: 400;
            margin: 5px 0;
        }

        .notification-card p strong {
            color: #333;
        }

        /* No Notifications */
        .no-notifications {
            text-align: center;
            font-size: 18px;
            font-weight: 500;
            color: #777;
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">Appointment Notifications</div>
    <nav class="nav-links">
        <a href="drdash.php">Dashboard</a>
        <a href="dpl.php">Appointments</a>
    </nav>
    <div class="dropdown">
        <button class="dropdown-btn">Settings </button>
        <div class="dropdown-content">
            <a href="drchange.php">Change Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<div class="content">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification-card">
                <h2>Upcoming Appointment</h2>
                <p><strong>Patient Name:</strong> <?= htmlspecialchars($row['PatientName']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($row['appointment_date']) ?></p>
                <p><strong>Time:</strong> <?= date('g:i A', strtotime($row['appointment_time'])) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($row['Patient_Phone']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($row['Patient_Email']) ?></p>
                <p><strong>Gender:</strong> <?= htmlspecialchars($row['Patient_Gender']) ?></p>
                <p><strong>Date of Birth:</strong> <?= htmlspecialchars($row['Patient_DOB']) ?></p>
                <p><strong>Marital Status:</strong> <?= htmlspecialchars($row['Patient_Marital_Status']) ?></p>
                <p><strong>Note:</strong> This appointment is scheduled within the next 24 hours.</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-notifications">
            <p>No upcoming notifications within the next 24 hours.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
