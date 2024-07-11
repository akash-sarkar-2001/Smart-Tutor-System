<!DOCTYPE html>
<html>
<head>
    <title>View Subjects</title>
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
        /* Custom CSS for hover effect */
        .list-group-item:hover {
            background-color: #f8f9fa; /* Change to the desired hover background color */
            cursor: pointer;
        }
    </style>
</head>
<body>
        <!-- Navigation -->
        <nav class="logout-link"> 
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-auto">
                    <a href="welcome_admin.php" class="text-white">Home</a>
                </div>
                <div class="col-auto">
                <a href="feedback.php" class="text-white">Feedback</a>
                </div>
                <div class="col-auto">
                    <a href="logout_admin.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php
        session_name("admin_session");
        session_start();

        // Database configuration
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "sts";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Ensure the user is logged in
        if (!isset($_SESSION['admin_username'])) {
            header("Location: admin_login.php");
            exit();
        }

        // Fetch the admin name and selected subjects for the logged-in admin
        $admin_username = $_SESSION['admin_username'];
        $stmt = $conn->prepare("SELECT admin_name, admin_subjects FROM admin_register WHERE admin_username = ?");
        $stmt->bind_param("s", $admin_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $admin_name = htmlspecialchars($row['admin_name']);
                echo "<h2 class='mt-4'>Directories for Admin: " . $admin_name . "</h2>";
                $subjects = explode(",", $row['admin_subjects']);
                sort($subjects); // Sort the subjects alphabetically
                echo "<ul class='list-group'>";
                foreach ($subjects as $subject) {
                    // Sanitize the subject name to match the directory name
                    $sanitizedSubject = preg_replace('/[^a-zA-Z0-9_\s&-]/', '_', $subject);
                    $subjectDir = './Notes/' . $sanitizedSubject;
                    if (is_dir($subjectDir)) {
                        echo "<li class='list-group-item'><a href='view_files_admin.php?subject=" . urlencode($sanitizedSubject) . "'>" . htmlspecialchars($subject) . "</a></li>";
                    }
                }
                echo "</ul>";
            }
        } else {
            echo "<div class='alert alert-warning mt-4'>No subjects found for the given username.</div>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
