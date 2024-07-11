<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit();
}

// Hide warning messages
error_reporting(0);
ini_set('display_errors', 0);

// Database connection
$conn = mysqli_connect("localhost", "root", "", "sts");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$admin_username = $_SESSION['admin_username'];
$stmt = $conn->prepare("SELECT admin_subjects FROM admin_register WHERE admin_username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$allowedDirs = explode(",", $row['admin_subjects']);
$stmt->close();
mysqli_close($conn);

$baseDir = 'Notes'; // Base directory to start browsing

// Function to display files and directories
function displayFiles($dir, $allowedDirs) {
    echo "<ul>";

    // List subdirectories
    $subdirectories = glob("$dir/*", GLOB_ONLYDIR);
    if ($dir !== 'Notes') {
        echo "<li class='folder'><a href='?dir=" . urlencode(dirname($dir)) . "'>Go Back</a></li>"; // Option to go back to the parent directory
    }
    foreach ($subdirectories as $subdir) {
        $dirName = basename($subdir);
        if (in_array($dirName, $allowedDirs)) {
            echo "<li class='folder'><a href='?dir=" . urlencode($subdir) . "'>" . htmlspecialchars($dirName) . " (Folder)</a></li>";
        }
    }

    // List files
    $files = glob("$dir/*");
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileName = basename($file);
            $dirName = basename(dirname($file));
            if (in_array($dirName, $allowedDirs)) {
                echo "<li class='file'><a href='" . htmlspecialchars($file) . "' target='_blank'>" . htmlspecialchars($fileName) . "</a><span class='delete-link' onclick='deleteItem(\"" . urlencode($file) . "\")'>Delete</span></li>";
            }
        }
    }

    echo "</ul>";
}

$currentDir = isset($_GET['dir']) ? $_GET['dir'] : $baseDir;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_file']) && isset($_POST['upload_dir'])) {
    $uploadDir = $_POST['upload_dir'];
    $file = $_FILES['upload_file'];
    if (in_array(basename($uploadDir), $allowedDirs) && $file['error'] == UPLOAD_ERR_OK) {
        $destination = $uploadDir . '/' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $uploadMessage = "File uploaded successfully to $uploadDir.";
        } else {
            $uploadMessage = "Error uploading file.";
        }
    } else {
        $uploadMessage = "Invalid directory or file.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>File Manager</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h2 {
            margin-bottom: 10px;
        }
        .folder {
            color: blue;
            font-weight: bold;
            cursor: pointer;
        }
        .file {
            color: green;
            cursor: pointer;
        }
        .delete-link {
            color: red;
            cursor: pointer;
            margin-left: 10px;
        }
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
            display: flex;
            justify-content: space-between;
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
        .flex-container {
            display: flex;
        }
        .directories-container, .upload-container {
            flex: 1;
            padding: 20px;
        }
        .upload-container {
            margin-left: 20px;
        }
        .file-name {
            max-width: 200px; /* Adjust as needed */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
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
        <div class="directories-container">
            <h2>Files and Subdirectories in '<?php echo htmlspecialchars($currentDir); ?>':</h2>
            <?php displayFiles($currentDir, $allowedDirs); ?>
        </div>
        <div class="upload-container">
            <h2 class="mt-4">Upload File</h2>

            <?php if (isset($uploadMessage)): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($uploadMessage); ?>
                </div>
            <?php endif; ?>

            <!-- Upload form -->
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="upload_dir">Upload to Directory:</label>
                    <select name="upload_dir" id="upload_dir" class="form-control">
                    <option value="">Select Directory</option>
                        <?php
                        foreach ($allowedDirs as $dir) {
                            echo "<option value='" . htmlspecialchars("Notes/$dir") . "'>" . htmlspecialchars($dir) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="upload_file">Choose File:</label>
                    <input type="file" name="upload_file" id="upload_file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary my-button">Upload File</button>
            </form>

            <!-- Button to redirect to notes_admin.php -->
            <a href="notes_admin.php" class="btn btn-primary my-button" style="background-color: #3f51b5; color: white;">Return to Notes Page</a>
        </div>
    </div>

    <script>
        function deleteItem(path) {
            if (confirm("Are you sure you want to delete this file?")) {
                window.location.href = "?delete=" + encodeURIComponent(path);
            }
        }
    </script>

    <?php
    // Handle deletion
    if (isset($_GET['delete'])) {
        $itemToDelete = urldecode($_GET['delete']); // Decode the URL-encoded path
        $dirName = basename(dirname($itemToDelete));
        if (in_array($dirName, $allowedDirs) && is_file($itemToDelete)) {
            unlink($itemToDelete); // Delete file
            // Refresh page after deletion
            header("Location: " . $_SERVER['PHP_SELF'] . "?dir=" . urlencode($currentDir));
            exit();
        }
    }
    ?>
</body>
</html>
