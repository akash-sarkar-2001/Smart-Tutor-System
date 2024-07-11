<?php
session_name("user_session");
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
if (!isset($_SESSION['rollno'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$rollno = $_SESSION['rollno'];

// Retrieve user information from the database
$stmt = $con->prepare("SELECT * FROM register WHERE rollno=?");
$stmt->bind_param("s", $rollno);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Read the classified_students.csv file and find the latest entry for the student
$csv_file = './csv_files/classified_students.csv';
$latest_student_info = null;
if (($handle = fopen($csv_file, "r")) !== FALSE) {
    // Assuming the first line contains the header names
    $header = fgetcsv($handle, 1000, ",");
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row = array_combine($header, $data);
        if ($row['rollno'] === $rollno) {
            if ($latest_student_info === null || strtotime($row['timestamp']) > strtotime($latest_student_info['timestamp'])) {
                $latest_student_info = $row;
            }
        }
    }
    fclose($handle);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Your CSS styles here */
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
<body>
    <!-- Navigation -->
    <nav class="logout-link"> 
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-auto">
                    <a href="welcome.php" class="text-white">Home</a>
                </div>
                <div class="col-auto">
                <a href="team.php" class="text-white">Contact Us</a>
                </div>
                <div class="col-auto">
                    <a href="logout.php" class="btn btn-danger">Logout</a>
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
                        <h1>Welcome <?php echo htmlspecialchars($user['fname']); ?></h1>
                    </div>
                    <div class="card-body">
                        <p><strong>Roll No:</strong> <?php echo htmlspecialchars($user['rollno']); ?></p>
                        <p><strong>Semester:</strong> <?php echo htmlspecialchars($user['sem']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <?php if ($latest_student_info): ?>
                            <!-- Display student's data -->
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($latest_student_info['category']); ?></p>
                            <!-- Assuming 'category' is the column name for the student category -->

                            <!-- Subject Lists -->
                            
                           
                        <?php else: ?>
                            <p><strong>Category: </strong> No data found for this roll number.</p>

                        <?php endif; ?>
                        <p><strong>Subjects Opted:</strong>
                        <?php
                            // Fetch subjects opted by the student from the database
                            $rollno = $_SESSION['rollno'];
                            $stmt = $con->prepare("SELECT selected_subject FROM register WHERE rollno=?");
                            $stmt->bind_param("s", $rollno);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $subjects = explode(",", $row['selected_subject']);
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
                        <a href="test.php" class="btn btn-primary btn-lg my-button">TEST</a>
                        <!-- Description Box -->
                        <p class="box">Give Test here.</p>
                    </div>
                    <!-- Notes Button -->
                    <div class="col text-center">
                        <a href="view_subject.php" class="btn btn-primary btn-lg my-button">File Viewer</a>
                        <!-- Description Box -->
                        <p class="box">Access all types of notes from here.</p>
                    </div>
                    <!-- Report Card Button -->
                    <div class="col text-center">
                        <a href="report_card.php" class="btn btn-primary btn-lg my-button" id="report">REPORT CARD</a>
                        <!-- Description Box -->
                        <p class="box">Report Card available here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
