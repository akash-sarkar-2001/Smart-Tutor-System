<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: lavender;
        }
        .table-custom {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        .table-custom th, .table-custom td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-custom th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
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
        .logout-link {
            background-color: #3f51b5;
            padding: 10px;
            color: white;
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
        <?php
        session_name("user_session");
        session_start();

        // Database connection parameters
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "sts";

        // Connect to the database
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch the logged-in user's roll number from the session
        if (isset($_SESSION['rollno'])) {
            $rollno = $_SESSION['rollno'];
        } else {
            header("Location: login.php");
            exit();
        }

        // Prepare and execute the SQL query using a prepared statement
        $sql = "SELECT rollno FROM login_ WHERE rollno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rollno);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists in the database
            $row = $result->fetch_assoc();
            $rollno = $row["rollno"];

            // Check if the CSV file exists for the user's roll number
            $csv_file = "./csv_files/{$rollno}.csv";
            if (file_exists($csv_file)) {
                // Display CSV file contents as a table
                echo "<h2 class='mt-4'>Report Card for Roll No: $rollno</h2>";
                echo "<table class='table-custom'>";
                if (($handle = fopen($csv_file, "r")) !== FALSE) {
                    // Read the CSV file
                    $header = fgetcsv($handle, 1000, ","); // Read the header row
                    echo "<thead><tr>";
                    foreach ($header as $col) {
                        echo "<th>{$col}</th>";
                    }
                    echo "</tr></thead><tbody>";
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        echo "<tr>";
                        foreach ($data as $cell) {
                            echo "<td>{$cell}</td>";
                        }
                        echo "</tr>";
                    }
                    fclose($handle);
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<div class='alert alert-danger'>CSV file for Roll No: $rollno not found.</div>";
            }

            // Check if the PNG file exists for the user's roll number
            $png_file = "./Graphs/{$rollno}.png";
            if (file_exists($png_file)) {
                // Append a timestamp to the image URL to prevent caching
                $timestamp = time();
                echo "<h3 class='mt-4'>Performance Graph</h3>";
                echo "<img src='{$png_file}?t={$timestamp}' class='img-fluid' alt='Performance Graph'>";
            } else {
                echo "<div class='alert alert-warning'>Graph for Roll No: $rollno not found.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>User not found.</div>";
        }

        // Close the statement and database connection
        $stmt->close();
        $conn->close();
        ?>
        <a href="welcome.php" class="btn btn-primary my-button" style="background-color: #3f51b5; color: white;">Return to Welcome page</a>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
