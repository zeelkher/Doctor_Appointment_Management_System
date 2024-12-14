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

$query = "SELECT * FROM patients WHERE Patient_Id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patientDetails = $result->fetch_assoc();
} else {
    echo "Patient details not found!";
    exit();
}



$profileImage = !empty($patientDetails['profile_img']) ? $patientDetails['profile_img'] : 'dp/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <title>Patient Dashboard</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            transition: all 0.3s ease;
            text-align: center;
            padding: 25px;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        .profile-card-header {
            background-color: #1abc9c;
            padding: 30px;
            color: #fff;
            border-radius: 15px 15px 0 0;
            border-bottom: 1px solid #ddd;
        }

        .profile-card-header img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 4px solid #fff;
            margin-bottom: 15px;
        }

        .profile-card-header h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .profile-card-header p {
            font-size: 16px;
            font-weight: 300;
            margin: 5px 0;
        }

        .profile-card-body {
            padding: 20px;
        }

        .profile-card-body ul {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 16px;
            font-weight: 500;
            display: grid;
            gap: 12px;
        }

        .profile-card-body ul li {
            display: grid;          
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .profile-card-body ul li span {
            text-align: left;
            font-weight: 300;
            color: #7f8c8d;
        }

        .profile-card-body ul li div {
            justify-self: left;
            font-weight: 500;
        }

        .profile-card-body ul li:last-child {
            border-bottom: none;
        }

        .edit-button {
            margin-top: 25px;
            display: inline-block;
            background-color: #1abc9c;
            color: #fff;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .edit-button:hover {
            background-color: #16a085;
        }

    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">Patient Dashboard</div>
    <nav class="nav-links">
        <a href="bookapp.php">Book Appointment</a>
        <a href="apph.php">Appointment History</a>
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
    <div class="profile-card">
        <div class="profile-card-header">
            <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Picture">
            <h2><?= htmlspecialchars($patientDetails['Patient_FirstName'] . ' ' . $patientDetails['Patient_LastName']) ?></h2>
            <p><?= htmlspecialchars($patientDetails['Patient_Gender']) ?></p>
        </div>
        <div class="profile-card-body">
            <ul>
                <li><span>Patient ID:</span> <div><?= htmlspecialchars($patientDetails['Patient_Id']) ?></div></li>
                <li><span>Marital Status:</span><div><?= htmlspecialchars($patientDetails['Patient_Marital_Status']) ?></div></li>
                <li><span>Date of Birth:</span> <div><?= htmlspecialchars($patientDetails['Patient_DOB']) ?></div></li>
                <li><span>Contact Number:</span> <div><?= htmlspecialchars($patientDetails['Patient_Phone']) ?></div></li>
                <li><span>Email:</span> <div><?= htmlspecialchars($patientDetails['Patient_Email']) ?></div></li>
                <li><span>Address:</span> <div><?= htmlspecialchars($patientDetails['Patient_Address']) ?></div></li>
            </ul>
            <a href="pedit.php?Patient_Id=<?= htmlspecialchars($patientDetails['Patient_Id']) ?>" class="edit-button">Edit Profile</a>
        </div>
    </div>
</div>

</body>
</html> 
