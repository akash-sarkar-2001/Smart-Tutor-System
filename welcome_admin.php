<?php
session_name("admin_session");
session_start();
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sts";

// Create a new database connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}


// Check if the user is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: login_admin.php"); // Redirect to login page if not logged in
    exit();
}

$admin_username = $_SESSION['admin_username'];

// Retrieve user information from the database
$stmt = $con->prepare("SELECT admin_name FROM admin_register WHERE admin_username=?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Retrieve subjects chosen by the admin
$stmt_subjects = $con->prepare("SELECT admin_subjects FROM admin_register WHERE admin_username=?");
$stmt_subjects->bind_param("s", $admin_username);
$stmt_subjects->execute();
$result_subjects = $stmt_subjects->get_result();
$subjects = array();
while ($row = $result_subjects->fetch_assoc()) {
    $subjects[] = $row['admin_subjects'];
}

// Display user information
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: lavender;
        }
        .logout-link {
            background-color: #3f51b5;
            padding: 10px;
            color: white;
        }
        .main-content {
            margin-top: 50px;
        }
        .card-container {
            margin-top: 20px;
        }
        .my-button {
            margin-top: 20px;
            width: 100%;
            border-radius: 20px;
            background-color: #3f51b5;
            color: white;
            border: none;
        }
        .my-button:hover {
            background-color: #303f9f;
        }
        .box {
            margin-top: 10px;
        }
    </style>
</head>
<body style="background-color: lavender;">
    
    <!-- Navigation -->
    <nav class="logout-link"> 
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-auto">
                    <a href="welcome_admin.php" class="text-white">Home</a>
                </div>
                <div class="col-auto">
                <a href="team.php" class="text-white">Contact Us</a>
                </div>
                <div class="col-auto">
                    <a href="logout_admin.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <!-- User Info Card -->
        <div class="row justify-content-center">
            <div class="col-md-6 card-container">
                <div class="card">
                    <div class="card-header text-center" style="background-color: #3f51b5; color: white;">
                        <h1>Welcome <?php echo htmlspecialchars($user['admin_name']); ?></h1>
                    </div>
                    <div class="card-body">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($admin_username); ?></p>
                        <p><strong>Subjects you choose to teach:</strong> 
                        <?php
                            // Fetch subjects opted by the student from the database
                            $admin_username = $_SESSION['admin_username'];
                            $stmt = $con->prepare("SELECT admin_subjects FROM admin_register WHERE admin_username=?");
                            $stmt->bind_param("s", $admin_username);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $subjects = explode(",", $row['admin_subjects']);
                                    echo "<ul>";
                                    foreach ($subjects as $subject) {
                                        echo "<li>" . htmlspecialchars($subject) . "</li>";
                                    }
                                    echo "</ul>";
                                }
                            } else {
                                echo "No subjects found.";
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons Section -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="row">
                    <!-- Test Button -->
                    <div class="col text-center">
                        <a href="test_upload.php" class="btn btn-primary btn-lg my-button">TEST</a>
                        <p class="box">Upload Questions and Delete</p>
                    </div>
                    <!-- Notes Button -->
                    <div class="col text-center">
                        <a href="file_manager.php" class="btn btn-primary btn-lg my-button">File Manager</a>
                        <!-- Description Box -->
                        <p class="box">Upload Notes and Delete</p>
                    </div>
                    <!-- Report Card Button -->
                    <div class="col text-center">
                        <a href="report_card_admin.php" class="btn btn-primary btn-lg my-button" id="report">REPORT CARD</a>
                        <!-- Description Box -->
                        <p class="box">Report Card available here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
