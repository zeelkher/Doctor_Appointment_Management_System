<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

// Define date constraints
$currentDate = date(format: 'Y-m-d');
$minDate = date('Y-m-d', strtotime('+1 day'));
$maxDate = date('Y-m-d', strtotime('+30 days'));

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "damsdb";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check session
if (!isset($_SESSION['Patient_Id'])) {
    header('Location: home.php');
    exit();
}

$patientId = $_SESSION['Patient_Id'];

// Get patient details
$patientFirstName = $patientLastName = $patientPhone = $patientAddress = '';
if ($stmt = $conn->prepare("SELECT Patient_FirstName, Patient_LastName, Patient_Phone, Patient_Address FROM patients WHERE Patient_Id = ?")) {
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $stmt->bind_result($patientFirstName, $patientLastName, $patientPhone, $patientAddress);
    $stmt->fetch();
    $stmt->close();
}

// Last booking date check
$lastBookingDate = null;
if ($stmt = $conn->prepare("SELECT appointment_date FROM appointments WHERE Patient_Id = ? ORDER BY appointment_date DESC LIMIT 1")) {
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $stmt->bind_result($lastBookingDate);
    $stmt->fetch();
    $stmt->close();
}

$canBook = true;
$message = '';

if ($lastBookingDate) {
    $lastBookingDateTime = new DateTime($lastBookingDate);
    $today = new DateTime();
    $todayPlus5 = (clone $lastBookingDateTime)->modify('+5 days');

    if ($today < $todayPlus5) {
        $canBook = false;
        $nextAvailableDate = $todayPlus5->format('Y-m-d');
        $message = "<div class='error'>You cannot book an appointment until $nextAvailableDate.</div>";
    }
}

// Success message
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "<div class='success'>Appointment booked successfully!</div>";
}

// Booking submission
// Booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($canBook) {
        $appointmentDate = $_POST['appointment_date'];
        $timeSlot = $_POST['time_slot'];
        $symptoms = $_POST['symptoms'];
        $comments = $_POST['comments'];

        // Ensure that the appointment date is not in the past
        $today = date('Y-m-d');
        if ($appointmentDate < $today) {
            $message = "<div class='error'>Appointment date cannot be in the past. Please select a valid date.</div>";
        } else {
            // Validate time
            $timeParts = explode(':', $timeSlot);
            $hours = intval($timeParts[0]);
            $minutes = intval($timeParts[1]);
            $minHours = 9;
            $maxHours = 21;

            if ($hours < $minHours || $hours > $maxHours || $minutes != 0) {
                $message = "<div class='error'>Invalid time selected. Please select a time between 9:00 AM and 9:00 PM, with minutes set to 00.</div>";
            } else {
                // Check if the appointment date and time already exist in the database
                if ($stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ?")) {
                    $stmt->bind_param("ss", $appointmentDate, $timeSlot);
                    $stmt->execute();
                    $stmt->bind_result($existingCount);
                    $stmt->fetch();
                    $stmt->close();

                    if ($existingCount > 0) {
                        $message = "<div class='error'>The selected date and time is already booked. Please choose a different slot.</div>";
                    } else {
                        // Insert the new appointment into the database
                        if ($stmt = $conn->prepare("INSERT INTO appointments (Patient_Id, appointment_date, appointment_time, App_Symptom, App_Comment, status) VALUES (?, ?, ?, ?, ?, 'pending')")) {
                            $stmt->bind_param("issss", $patientId, $appointmentDate, $timeSlot, $symptoms, $comments);
                            if ($stmt->execute()) {
                                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                                exit();
                            } else {
                                $message = "<div class='error'>Error: " . $stmt->error . "</div>";
                            }
                            $stmt->close();
                        } else {
                            $message = "<div class='error'>Error preparing statement: " . $conn->error . "</div>";
                        }
                    }
                } else {
                    $message = "<div class='error'>Error checking for existing appointment: " . $conn->error . "</div>";
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
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
    padding: 0 10px;
}

.form-container {
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 100%;
    max-width: 600px;
}

.form-container h2 {
    text-align: center;
    font-size: 24px;
    margin-bottom: 20px;
}

/* Make all form fields the same size */
.form-container input[type="date"],
.form-container textarea {
    width: 95%; /* Ensures all fields take up full width */
    padding: 12px;
    margin-bottom: 15px; /* Same margin for all fields */
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}
.form-container select{
    width: 99.5%; /* Ensures all fields take up full width */
    padding: 12px;
    margin-bottom: 15px; /* Same margin for all fields */
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}

/* Ensures consistent styling for all inputs (including time slot and date) */
.form-container input[type="date"],
.form-container select {
    padding-left: 12px;
    padding-right: 12px;
}

.form-container input[type="submit"] {
    background-color: #1abc9c;
    color: #fff;
    padding: 12px 30px;
    border-radius: 25px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
}

.form-container input[type="submit"]:hover {
    background-color: #16a085;
}

.error {
    color: red;
    font-size: 16px;
    text-align: center;
}

.success {
    color: green;
    font-size: 16px;
    text-align: center;
}

/* Media Queries for responsiveness */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar .nav-links {
        flex-direction: column;
        gap: 10px;
    }

    .form-container {
        padding: 20px;
    }

    .form-container input[type="date"],
    .form-container select,
    .form-container textarea {
        font-size: 14px;
    }

    .form-container input[type="submit"] {
        font-size: 14px;
        padding: 10px 25px;
    }
}

@media (max-width: 480px) {
    .form-container {
        max-width: 100%;
        padding: 15px;
    }

    .form-container h2 {
        font-size: 20px;
    }

    .form-container input[type="submit"] {
        font-size: 14px;
        padding: 10px 20px;
    }
}

    </style>
</head>
<body>
<header class="navbar">
    <div class="logo">Book Appointment</div>
    <nav class="nav-links">
        <a href="pdash.php">Profile</a>
        <a href="apph.php">Appointment History</a>
        <a href="noti.php">Notifications</a>
        <div class="dropdown">
            <button class="dropdown-btn">Settings</button>
            <div class="dropdown-content">
                <a href="cp.php">Change Password</a>
                <a href="logout.php" style="background-color: #e74c3c;">Logout</a>
            </div>
        </div>
    </nav>
</header>

<div class="content">
    <div class="form-container">
        <h2>Book an Appointment</h2>
        <?php echo $message; ?>
        <form method="POST">
            <label for="appointment_date">Choose Appointment Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>" required <?php if (!$canBook) echo 'disabled'; ?>>

            <label for="time_slot">Choose a Time Slot:</label>
            <select id="time_slot" name="time_slot" required <?php if (!$canBook) echo 'disabled'; ?>>
                <option value="">Select a Time Slot</option>
                <?php
                for ($hour = 9; $hour <= 21; $hour++) {
                    $time24 = sprintf('%02d:00', $hour);
                    $time12 = date("g:i A", strtotime($time24));
                    echo "<option value=\"$time24\">$time12</option>";
                }
                ?>
            </select>

            <label for="symptoms">Symptoms:</label>
            <textarea id="symptoms" name="symptoms" rows="4" required <?php if (!$canBook) echo 'disabled'; ?>></textarea>

            <label for="comments">Additional Comments:</label>
            <textarea id="comments" name="comments" rows="4" <?php if (!$canBook) echo 'disabled'; ?>></textarea>

            <input type="submit" value="Book Appointment" <?php if (!$canBook) echo 'disabled'; ?>>
        </form>
    </div>
</div>
</body>
</html>
