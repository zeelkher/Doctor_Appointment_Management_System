<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Dashboard</title>
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
}

/* Profile Card */
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

/* Profile Details */
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

/* Table */
.table-container {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #1abc9c;
    color: white;
}

table td {
    font-size: 14px;
}

table tr:hover {
    background-color: #f1f2f6;
}

/* Form and Buttons */
form input[type="text"],
form input[type="date"],
form button {
    padding: 10px;
    font-size: 14px;
    margin-right: 10px;
    border-radius: 5px;
}

form button {
    background-color: #1abc9c;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #16a085;
}

/* Select Status */
.select-status {
    padding: 5px;
    font-size: 14px;
    border-radius: 5px;
}

/* Action Button */
.action-button {
    background-color: #1abc9c;
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 4px;
}

.action-button:hover {
    background-color: #16a085;
}
</style>

</head>
<body>
<header class="navbar">
    <div class="logo">Appointments</div>
    <nav class="nav-links">
        <a href="drdash.php">Dashboard</a>
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

<div class="content">
    <h2>Appointment History</h2>

    
    <form method="GET" action="">
        <input type="text" name="search_id" placeholder="App ID" value="<?php echo isset($_GET['search_id']) ? htmlspecialchars($_GET['search_id']) : ''; ?>">
        <input type="text" name="search_patient" placeholder="Patient Name" value="<?php echo isset($_GET['search_patient']) ? htmlspecialchars($_GET['search_patient']) : ''; ?>">
        <input type="date" name="search_date" value="<?php echo isset($_GET['search_date']) ? htmlspecialchars($_GET['search_date']) : ''; ?>">
        <input type="text" name="search_patient_id" placeholder="Patient ID" value="<?php echo isset($_GET['search_patient_id']) ? htmlspecialchars($_GET['search_patient_id']) : ''; ?>">
        <button type="submit" class="action-button">Search</button>
        
        <button type="submit" name="reset_filters" value="1" class="all-list-button">All List</button>
    </form>

    <form method="POST">
        <table>
            <tr>
                <th>App ID</th>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Symptom</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            
            $db_host = 'localhost';
            $db_username = 'root'; 
            $db_password = ''; 
            $db_name = 'damsdb';

            $conn = new mysqli($db_host, $db_username, $db_password, $db_name);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            
            $searchId = isset($_GET['search_id']) ? $_GET['search_id'] : '';
            $searchPatient = isset($_GET['search_patient']) ? $_GET['search_patient'] : '';
            $searchDate = isset($_GET['search_date']) ? $_GET['search_date'] : '';
            $searchPatientId = isset($_GET['search_patient_id']) ? $_GET['search_patient_id'] : '';

            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['status']) && is_array($_POST['status'])) {
                    foreach ($_POST['status'] as $appId => $newStatus) {
                        $updateSql = "UPDATE appointments SET status = ? WHERE App_Id = ?";
                        $stmt = $conn->prepare($updateSql);
                        $stmt->bind_param("si", $newStatus, $appId);
                        $stmt->execute();
                    }
                }
            }

            
            if (isset($_GET['reset_filters']) && $_GET['reset_filters'] == '1') {
                $searchId = $searchPatient = $searchDate = $searchPatientId = '';
            }

            
            $sql = "SELECT 
                        appointments.App_Id, 
                        appointments.Patient_Id, 
                        appointments.App_Symptom, 
                        appointments.App_Comment, 
                        appointments.status, 
                        appointments.appointment_date, 
                        appointments.appointment_time, 
                        CONCAT(patients.Patient_FirstName, ' ',  
                               patients.Patient_LastName) AS Patient_Name 
                    FROM appointments 
                    JOIN patients ON appointments.Patient_Id = patients.Patient_Id 
                    WHERE 1 ";

            
            if (!empty($searchId)) {
                $sql .= " AND appointments.App_Id LIKE '%" . $conn->real_escape_string($searchId) . "%'";
            }
            if (!empty($searchPatient)) {
                $sql .= " AND CONCAT(patients.Patient_FirstName, ' ', patients.Patient_LastName) LIKE '%" . $conn->real_escape_string($searchPatient) . "%'";
            }
            if (!empty($searchDate)) {
                $sql .= " AND appointments.appointment_date = '" . $conn->real_escape_string($searchDate) . "'";
            }
            if (!empty($searchPatientId)) {
                $sql .= " AND appointments.Patient_Id LIKE '%" . $conn->real_escape_string($searchPatientId) . "%'";
            }

            
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['App_Id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Patient_Id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Patient_Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['App_Symptom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['App_Comment']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['appointment_time']) . "</td>";
                    echo "<td><select name='status[" . $row['App_Id'] . "]'>
                            <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                            <option value='completed' " . ($row['status'] == 'completed' ? 'selected' : '') . ">Completed</option>
                            <option value='cancelled' " . ($row['status'] == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                          </select></td>";
                    echo "<td><button type='submit' class='action-button'>Update</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9' class='no-data'>No appointments found</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </form>
</div>
</body>
</html>
