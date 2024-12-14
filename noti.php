<?php
include_once('db.php'); 
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");

if (!isset($_SESSION['Patient_Id'])) {
    header('Location: home.php');
    exit();
}

$patientId = $_SESSION['Patient_Id'];

$query = "
    SELECT a.App_Id, a.appointment_date, a.appointment_time, 
        CONCAT(p.Patient_FirstName, ' ', p.Patient_LastName) AS PatientName
    FROM appointments a
    JOIN patients p ON a.Patient_Id = p.Patient_Id
    WHERE a.Patient_Id = ?
    AND a.status = 'pending'
    AND TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.appointment_date, ' ', a.appointment_time)) BETWEEN 0 AND 24
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <title>Notifications</title>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f1f2f6;
            color: #333;
        }

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
            position: relative; /* Important for dropdown content */
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

        .navbar .dropdown:hover .dropdown-content {
            display: block;
        }

        .navbar .dropdown-content a {
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            font-weight: 400;
        }

        .navbar .dropdown-content a:hover {
            background-color: #1abc9c;
        }

        .navbar .dropdown-content a {
            border-radius: 0 !important;
            transition: background-color 0.3s ease;
        }

        .navbar .dropdown-content a:hover {
            background-color: #1abc9c;
            transform: none;
        }

        .navbar .dropdown-content a[style*="background-color: #e74c3c;"] {
            border-radius: 0 !important;
        }

        .content {
            margin: 40px auto;
            padding: 0 20px;
        }

        .notification-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .notification-card:hover {
            transform: translateY(-5px);
        }

        .notification-card h2 {
            font-size: 20px;
            color: #2C3E50;
        }

        .notification-card p {
            font-size: 16px;
            color: #7f8c8d;
        }

        .no-notifications {
            text-align: center;
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">Appointment Notification</div>
    <nav class="nav-links">
        <a href="pdash.php">Profile</a>
        <a href="bookapp.php">Book Appointment</a>
        <a href="apph.php">Appointment History</a>
        <div class="dropdown">
            <button class="dropdown-btn">Settings</button>
            <div class="dropdown-content">
                <a href="cp.php">Change Password</a>
                <a href="logout.php" style="background-color: #e74c3c;">Log Out</a>
            </div>
        </div>
    </nav>
</header>

<div class="content">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification-card">
                <h2>Upcoming Appointment</h2>
                <p><strong>Patient Name:</strong> <?= htmlspecialchars($row['PatientName']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($row['appointment_date']) ?></p>
                <p><strong>Time:</strong> <?= date('g:i A', strtotime($row['appointment_time'])) ?></p>
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
