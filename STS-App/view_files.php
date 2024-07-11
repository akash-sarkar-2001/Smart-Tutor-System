<?php
session_name("user_session");
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['rollno'])) {
    header("Location: login.php");
    exit();
}

// Get the subject directory from the query parameter
$subject = $_GET['subject'];
$subjectDir = './Notes/' . $subject;

// List the files in the subject directory
if (is_dir($subjectDir)) {
    $files = scandir($subjectDir);
} else {
    echo "Subject directory not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($subject); ?> Directory</title>
</head>
<body>
    <h2><?php echo htmlspecialchars($subject); ?> Files</h2>
    <ul>
        <?php
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li><a href='$subjectDir/$file'>" . htmlspecialchars($file) . "</a></li>";
            }
        }
        ?>
    </ul>
</body>
</html>
