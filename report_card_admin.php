<?php
session_name("admin_session");
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit();
}

// Function to parse CSV file and return data as array
function parseCSV($file) {
    $csvData = []; // Initialize an empty array to store CSV data

    // Open the CSV file for reading
    if (($handle = fopen($file, "r")) !== FALSE) {
        // Read the first row to get the header names
        $header = fgetcsv($handle, 1000, ",");
        
        // Read each subsequent line of the file until the end is reached
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Combine header names with data and store as associative array
            $csvData[] = array_combine($header, $data);
        }
        fclose($handle); // Close the file
    } else {
        echo "Error: Unable to open file.";
    }

    return $csvData; // Return the array containing CSV data
}

// Function to get all image files from a directory
function getGraphImages($dir) {
    $images = [];
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== FALSE) {
                if ($file != "." && $file != ".." && (strpos($file, '.png') !== FALSE || strpos($file, '.jpg') !== FALSE || strpos($file, '.jpeg') !== FALSE)) {
                    $images[] = $file;
                }
            }
            closedir($dh);
        }
    } else {
        echo "Error: Directory does not exist.";
    }
    return $images;
}

// Path to the CSV file
$csv_file = 'csv_files/students.csv';
$csvData = parseCSV($csv_file); // Parse the CSV file

// Path to the directory containing graphs
$graph_dir = 'Graphs-admin';
$graphImages = getGraphImages($graph_dir); // Get the list of graph images

// HTML for displaying CSV data as a table
$tableHTML = "<table class='table table-bordered table-custom'>";
$tableHTML .= "<thead class='thead-dark'><tr><th>Subject Name</th><th>Roll Number</th><th>Obtained Marks</th><th>Total Marks</th><th>Timestamp</th></tr></thead>";
$tableHTML .= "<tbody>";
foreach ($csvData as $row) {
    $tableHTML .= "<tr><td>{$row['subject-name']}</td><td>{$row['rollno']}</td><td>{$row['obtained_marks']}</td><td>{$row['total_marks']}</td><td>{$row['timestamp']}</td></tr>";
}
$tableHTML .= "</tbody>";
$tableHTML .= "</table>";

// HTML for displaying graphs
$graphsHTML = "<div class='graph-container'>";
foreach ($graphImages as $image) {
    $graphsHTML .= "<div class='graph-box'><img src='$graph_dir/$image' alt='$image' class='img-fluid'></div>";
}
$graphsHTML .= "</div>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
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
        .table-custom {
            margin-top: 20px;
        }
        .graph-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .graph-box {
            flex: 1 1 300px;
        }
        .graph-box img {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            background: white;
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
                <a href="team.php" class="text-white">Contact Us</a>
                </div>
                <div class="col-auto">
                    <a href="logout_admin.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <?php echo $tableHTML; ?> <!-- Display CSV data as a table -->
        <?php echo $graphsHTML; ?> <!-- Display graphs -->
        <a href="welcome_admin.php" class="btn btn-primary my-button" style="background-color: #3f51b5; color: white;">Return to Welcome page</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
