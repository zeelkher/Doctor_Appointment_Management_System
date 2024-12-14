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

if ($stmt = $conn->prepare("
    SELECT a.App_Id, a.App_Symptom, a.App_Comment, a.appointment_date, a.appointment_time, a.status, p.Patient_FirstName, p.Patient_LastName 
    FROM appointments a 
    JOIN patients p ON a.Patient_Id = p.Patient_Id 
    WHERE a.Patient_Id = ? 
    ORDER BY a.appointment_date DESC")) {
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $stmt->bind_result($appId, $appSymptom, $appComment, $appointmentDate, $appointmentTime, $status, $patientFirstName, $patientLastName);
    $appointments = [];
    while ($stmt->fetch()) {
        $appointments[] = [
            'App_Id' => $appId,
            'App_Symptom' => $appSymptom,
            'App_Comment' => $appComment,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'status' => $status,
            'Patient_FirstName' => $patientFirstName,
            'Patient_LastName' => $patientLastName
        ];
    }
    $stmt->close();
} else {
    $appointments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <title>Appointment History</title>
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
            border-radius: 0;
            transition: none;
        }

        .navbar .dropdown:hover .dropdown-content {
            display: block;
        }

        .navbar .dropdown-content a:hover {
            background-color: #1abc9c;
            transform: none;
        }

        .navbar .dropdown-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            padding: 12px 20px;
            transition: none;
        }

        .navbar .dropdown-btn:hover {
            background-color: transparent;
        }

        .content {
            margin: 40px auto;
            max-width: 1000px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .content h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #1abc9c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px 10px;
            text-align: left;
        }

        table th {
            background-color: #1abc9c;
            color: #fff;
        }

        table tr:hover {
            background-color: #f1f2f6;
        }

        .no-appointments {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin: 30px 0;
        }
    </style>
</head>
<body>
<header class="navbar">
    <div class="logo">Appointment History</div>
    <nav class="nav-links">
        <a href="pdash.php">Profile</a>
        <a href="bookapp.php">Book Appointment</a>
        <a href="noti.php">Notifications</a>
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
    <h2>Appointment History</h2>
    <?php if (empty($appointments)): ?>
        <div class="no-appointments">No appointments found!</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Name</th>
                    <th>Symptoms</th>
                    <th>Comments</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['App_Id']) ?></td>
                        <td><?= htmlspecialchars($appointment['Patient_FirstName']) . ' ' . htmlspecialchars($appointment['Patient_LastName']) ?></td>
                        <td><?= htmlspecialchars($appointment['App_Symptom']) ?></td>
                        <td><?= htmlspecialchars($appointment['App_Comment']) ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                        <td><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></td>
                        <td><?= htmlspecialchars($appointment['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
