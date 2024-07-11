<!DOCTYPE html>
<html>
<head>
    <title>View Subjects</title>
    <!-- Bootstrap CSS -->
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
        .list-group-item:hover {
            background-color: #f8f9fa;
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
    <div class="container">
        <?php
        session_name("user_session");
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
        if (!isset($_SESSION['rollno'])) {
            header("Location: login.php");
            exit();
        }

        // Fetch the selected subjects for the logged-in student
        $rollno = $_SESSION['rollno'];
        $stmt = $conn->prepare("SELECT selected_subject FROM register WHERE rollno = ?");
        $stmt->bind_param("s", $rollno);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2 class='mt-4'>Directories for Roll Number: " . htmlspecialchars($rollno) . "</h2>";
            echo "<ul class='list-group'>";
            while ($row = $result->fetch_assoc()) {
                $subjects = explode(",", $row['selected_subject']);
                foreach ($subjects as $subject) {
                    // Sanitize the subject name to match the directory name
                    $sanitizedSubject = preg_replace('/[^a-zA-Z0-9_\s&-]/', '_', $subject);
                    $subjectDir = './Notes/' . $sanitizedSubject;
                    if (is_dir($subjectDir)) {
                        echo "<li class='list-group-item'><a href='view_files.php?subject=" . urlencode($sanitizedSubject) . "'>" . htmlspecialchars($subject) . "</a></li>";
                    }
                }
            }
            echo "</ul>";
        } else {
            echo "<p class='mt-4'>No subjects found for the given roll number.</p>";
        }

        // Display PDF files based on roll number
        echo "<h2 class='mt-4'>Highlight PDFs that need to be studied</h2>";
        $highlightDir = './highlight/';
        $pdfFiles = glob($highlightDir . $rollno . '*.pdf'); // This will match 2003222.pdf, 2003222_1.pdf, etc.
        if (count($pdfFiles) > 0) {
            echo "<ul class='list-group'>";
            foreach ($pdfFiles as $pdfFile) {
                $fileName = basename($pdfFile);
                echo "<li class='list-group-item'><a href='" . htmlspecialchars($pdfFile) . "' target='_blank'>" . htmlspecialchars($fileName) . "</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='mt-4'>No highlight PDFs found for the given roll number.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
