<?php
include_once('db.php');
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['Doctor_Id'])) {
    header('Location: home.php');
    exit();
}

$doctorId = $_SESSION['Doctor_Id'];


$totalAppointmentsQuery = "SELECT COUNT(*) as total FROM appointments";
$resultTotalAppointments = mysqli_query($conn, $totalAppointmentsQuery);
$totalAppointments = $resultTotalAppointments ? mysqli_fetch_assoc($resultTotalAppointments)['total'] : 0;


$todayDate = date('Y-m-d');
$todayAppointmentsQuery = "SELECT COUNT(*) as today FROM appointments WHERE appointment_date = '$todayDate'";
$resultTodayAppointments = mysqli_query($conn, $todayAppointmentsQuery);
$todayAppointments = $resultTodayAppointments ? mysqli_fetch_assoc($resultTodayAppointments)['today'] : 0;


$malePatientsQuery = "SELECT COUNT(*) as male FROM patients WHERE Patient_Gender = 'Male'";
$resultMalePatients = mysqli_query($conn, $malePatientsQuery);
$malePatients = $resultMalePatients ? mysqli_fetch_assoc($resultMalePatients)['male'] : 0;

$femalePatientsQuery = "SELECT COUNT(*) as female FROM patients WHERE Patient_Gender = 'Female'";
$resultFemalePatients = mysqli_query($conn, $femalePatientsQuery);
$femalePatients = $resultFemalePatients ? mysqli_fetch_assoc($resultFemalePatients)['female'] : 0;


$doctorDetailsQuery = "SELECT * FROM doctor WHERE Doctor_Id = $doctorId";
$resultDoctorDetails = mysqli_query($conn, $doctorDetailsQuery);

if ($resultDoctorDetails && mysqli_num_rows($resultDoctorDetails) > 0) {
    $doctorDetails = mysqli_fetch_assoc($resultDoctorDetails);
} else {
    
    $doctorDetails = [
        'Doctor_FirstName' => '',
        'Doctor_LastName' => '',
        'Doctor_specialities' => '',
        'Doctor_Phone' => '',
        'Doctor_Email' => '',
        'Doctor_DOB' => '',
        'profile_img' => 'img/dr.jpeg.png'
    ];
}


$profileImage = !empty($doctorDetails['profile_img']) ? $doctorDetails['profile_img'] : 'img/dr.jpeg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <title>Doctor Dashboard</title>
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

        .navbar .dropdown-content a:last-child:hover {
            background-color: red;
        }

        .content {
            margin: 40px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .card-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 250px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .card p {
            color: #1abc9c;
            font-size: 35px;
            font-weight: 700;
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
    <div class="logo">Doctor Dashboard</div>
    <nav class="nav-links">
        <a href="dpl.php">Appointments</a>
        <a href="dnoti.php">Upcoming Patients</a>
    </nav>
    <div class="dropdown">
        <button class="dropdown-btn">Settings </button>
        <div class="dropdown-content">
            <a href="drchange.php">Change Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<main class="content">
    <div class="card-container">
        <div class="card">
            <h2>Total Appointments</h2>
            <p><?= $totalAppointments ?></p>
        </div>
        <div class="card">
            <h2>Today's Appointments</h2>
            <p><?= $todayAppointments ?></p>
        </div>
        <div class="card">
            <h2>Male Patients</h2>
            <p><?= $malePatients ?></p>
        </div>
        <div class="card">
            <h2>Female Patients</h2>
            <p><?= $femalePatients ?></p>
        </div>
    </div>

    <div class="profile-card">
        <div class="profile-card-header">
            <img src="<?= $profileImage ?>" alt="Doctor's Profile Image">
            <h2><?= htmlspecialchars($doctorDetails['Doctor_FirstName'] . ' ' . $doctorDetails['Doctor_LastName']) ?></h2>
            <p><?= htmlspecialchars($doctorDetails['Doctor_specialities']) ?></p>
        </div>
        <div class="profile-card-body">
            <ul>
                <li><span>Email:</span> <div><?= htmlspecialchars($doctorDetails['Doctor_Email']) ?></div></li>
                <li><span>Phone:</span> <div><?= htmlspecialchars($doctorDetails['Doctor_Phone']) ?></div></li>
                <li><span>Date of Birth:</span> <div><?= date('d-m-Y', strtotime($doctorDetails['Doctor_DOB'])) ?></div>
                </li>
            </ul>
        </div>
        <a href="dedit.php" class="edit-button">Edit Profile</a>
    </div>
</main>

</body>
</html>
