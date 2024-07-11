<?php
session_name("admin_session");
session_start();

// Database connection setup
$host = 'localhost'; // replace with your database host
$db = 'sts'; // replace with your database name
$user = 'root'; // replace with your database username
$pass = ''; // replace with your database password

$con = new mysqli($host, $user, $pass, $db);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch subjects opted by the admin from the database
$admin_username = $_SESSION['admin_username'];
$stmt = $con->prepare("SELECT admin_subjects FROM admin_register WHERE admin_username=?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $subjects = explode(",", $row['admin_subjects']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        if (isset($_FILES['pdf']) && isset($_FILES['txt'])) {
            $pdfFile = $_FILES['pdf'];
            $txtFile = $_FILES['txt'];
            $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);

            if ($pdfFile['error'] === UPLOAD_ERR_OK && $txtFile['error'] === UPLOAD_ERR_OK) {
                // Delete previous files in the directory
                $files = glob('./test_today/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                $pdfDestination = './test_today/' . basename($pdfFile['name']);
                $txtDestination = './test_today/' . basename($txtFile['name']);

                if (move_uploaded_file($pdfFile['tmp_name'], $pdfDestination) && move_uploaded_file($txtFile['tmp_name'], $txtDestination)) {
                    $message = "Files saved successfully.";
                    
                    // Overwrite the subject file with the new subject
                    $subjectFile = './Sub-Today/selected_subject.txt';
                    file_put_contents($subjectFile, $subject . PHP_EOL);
                } else {
                    $message = "Error occurred while saving files.";
                }
            } else {
                $message = "Error uploading files.";
            }
        } else {
            $message = "Please select both PDF and TXT files.";
        }
    }

    if (isset($_POST['delete_all'])) {
        $files = glob('./test_today/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
        $subjectFile = './Sub-Today/selected_subject.txt';
        if (is_file($subjectFile)) {
            unlink($subjectFile); // delete subject file
        }
        $message = "All files deleted successfully.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Choose Question and Answer</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Select2 JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
    .flex-container {
        display: flex;
        justify-content: space-between;
    }
    .form-container, .pdf-container {
        flex: 1;
        padding: 20px;
    }
    .pdf-container {
        margin-left: 20px;
    }
    iframe {
        width: 100%;
        height: 560px;
        border: 1px solid #ccc;
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
    <div class="flex-container">
        <div class="form-container">
            <h2 class="mt-5">Select Question and Answers</h2>
            <h6>*Please make sure to delete all previous files before selecting new files from the directory*</h6>
            <?php if (isset($message)): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="pdf" class="form-label">Upload PDF File (Question):</label>
                    <input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label for="txt" class="form-label">Upload TXT File (Answer):</label>
                    <input type="file" name="txt" id="txt" class="form-control" accept=".txt">
                </div>
                <div class="form-group">
                    <label for="subject" class="form-label">Subject name</label>
                    <select name="subject" id="subject" class="form-select select2">
                        <option value="">Select Subject</option>
                        <?php
                        foreach ($subjects as $subject) {
                            echo "<option value=\"" . htmlspecialchars($subject) . "\">" . htmlspecialchars($subject) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn btn-primary my-button">Save Files</button>
                <button type="submit" name="delete_all" class="btn btn-danger my-button">Delete All Files</button>
                <a href='welcome_admin.php' class='btn btn-primary my-button' style="background-color: #3f51b5; color: white;">Return to Welcome page</a>
            </form>
        </div>
        <div class="pdf-container">
            <iframe src="./test_format/test_format.pdf"></iframe>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Search files"
        });
    });
</script>
</body>
</html>

