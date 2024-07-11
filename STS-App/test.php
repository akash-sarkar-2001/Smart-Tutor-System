<?php
session_name("user_session");
session_start();

// Set Indian Standard Time zone
date_default_timezone_set('Asia/Kolkata');

// Check if the user is logged in
if (!isset($_SESSION['rollno'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Include database connection file
include_once 'test_connect.php';

// Define the directory path
$dirPath = './test_today/';

// Automatically select the PDF and TXT files from the directory
$files = glob($dirPath . '*'); // Get all file names

// Check if there are any files available
if (empty($files)) {
    // If no files are available, display alert message and redirect to welcome.php
    echo '<script>window.onload = function() { alert("NO TEST AVAILABLE"); window.location.href = "welcome.php"; }</script>';
    exit();
}

foreach($files as $file) { // Iterate files
    if(is_file($file) && pathinfo($file, PATHINFO_EXTENSION) == 'pdf') {
        $_SESSION['selected_pdf'] = basename($file);
    }
    if(is_file($file) && pathinfo($file, PATHINFO_EXTENSION) == 'txt') {
        $_SESSION['selected_txt'] = basename($file);
    }
}

// Check if PDF file is selected
if (!isset($_SESSION['selected_pdf'])) {
    // If no PDF file is selected, display a popup box
    echo '<script>window.onload = function() { alert("NO TEST AVAILABLE"); window.location.href = "welcome.php"; }</script>';
    exit();
}

if (!isset($_SESSION['selected_txt'])) {
    // If no TXT file is selected, display a popup box
    echo '<script>window.onload = function() { alert("NO TEST AVAILABLE"); window.location.href = "welcome.php"; }</script>';
    exit();
}

$selectedPdf = $dirPath . $_SESSION['selected_pdf'];

// Check if the text file is selected and load its contents
$answers = array(); // Initialize an empty array for answers
if (isset($_SESSION['selected_txt']) && !empty($_SESSION['selected_txt'])) {
    $selectedTxt = $dirPath . $_SESSION['selected_txt'];
    $answers = readTextFile($selectedTxt);
}

// Function to read the contents of a text file and return an array of lines
function readTextFile($filePath) {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES);
    return $lines;
}

// Function to read the subject name from the selected_subject.txt file
function getSubjectName($filePath) {
    return file_get_contents($filePath);
}

// Get the subject name from the selected_subject.txt file
$subjectFilePath = './Sub-Today/selected_subject.txt';
if (file_exists($subjectFilePath)) {
    $subject = getSubjectName($subjectFilePath);
    $_SESSION['selected_subject'] = trim($subject);
} else {
    echo '<script>window.onload = function() { alert("SUBJECT NAME FILE NOT FOUND"); window.location.href = "welcome.php"; }</script>';
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if answers are provided
    if(isset($_POST['answers'])) {
        // Get submitted answers from the form
        $submittedAnswers = $_POST['answers'];

        // Get roll number from the session
        $rollNumber = $_SESSION['rollno'];

        // Get selected subject from the session
        $subject = $_SESSION['selected_subject'];

        // Calculate marks (1 mark per correct answer)
        $marks = array();
        $wrongResponses = array();
        foreach ($answers as $index => $answer) {
            $mark = isset($submittedAnswers[$index]) && $submittedAnswers[$index] == $answer ? 1 : 0;
            $marks[] = $mark;
            if ($mark == 0) {
                $wrongResponses[$index] = $submittedAnswers[$index];
            }
        }

        // Calculate total marks
        $totalMarks = count($answers);

        // Calculate obtained marks
        $obtainedMarks = array_sum($marks);

        // Validate and sanitize the roll number input
        $rollNumber = filter_var($rollNumber, FILTER_SANITIZE_STRING);

        // Generate the CSV file name for students.csv
        $studentsFileName = 'csv_files/students.csv';
        
        // Generate the CSV file name for the roll number
        $rollNumberFileName = 'csv_files/' . $rollNumber . '.csv';

        // Write marks to CSV file along with roll number, obtained marks, total marks, and timestamp for students.csv
        $studentsFileHandle = fopen($studentsFileName, 'a'); // Open in append mode
        if ($studentsFileHandle) {
            // If the file is empty, write the header
            if (filesize($studentsFileName) === 0) {
                fputcsv($studentsFileHandle, array('subject-name','rollno', 'obtained_marks', 'total_marks', 'timestamp')); // Write header names
            }
            // Write subject name, roll number, obtained marks, total marks, and timestamp
            fputcsv($studentsFileHandle, array($subject, $rollNumber, $obtainedMarks, $totalMarks, date('Y-m-d H:i:s')));
            fclose($studentsFileHandle);
        } else {
            echo "Failed to write marks to students file.";
        }
        
        // Write marks to CSV file along with PDF file name, obtained marks, total marks, and timestamp for the roll number
        $rollNumberFileHandle = fopen($rollNumberFileName, 'a'); // Open in append mode
        if ($rollNumberFileHandle) {
            // Extract just the file name from the path
            $pdfFileName = basename($selectedPdf);
            $timestamp = date('Y-m-d H:i:s');
            if (filesize($rollNumberFileName) === 0) {
                fputcsv($rollNumberFileHandle, array('subject-name', 'pdf_name', 'obtained_marks', 'total_marks', 'timestamp')); // Write header names
            }
            fputcsv($rollNumberFileHandle, array($subject, $pdfFileName, $obtainedMarks, $totalMarks, $timestamp)); // Write subject name, PDF file name, obtained marks, total marks, and timestamp
            fclose($rollNumberFileHandle);
        } else {
            echo "Failed to write marks to roll number file.";
        }

        // Write submitted answers to a text file named as roll_number.txt inside ./response_sheet
        $responsesDir = './response_sheet/';
        if (!is_dir($responsesDir)) {
            mkdir($responsesDir, 0777, true); // Create directory if it doesn't exist
        }
        $responsesFileName = $responsesDir . $rollNumber . '.txt';
        $responsesFileHandle = fopen($responsesFileName, 'w'); // Open in write mode
        if ($responsesFileHandle) {
            foreach ($submittedAnswers as $index => $answer) {
                fwrite($responsesFileHandle, "q" . ($index + 1) . ": " . $answer . PHP_EOL);
            }
            fclose($responsesFileHandle);
        } else {
            echo "Failed to write responses to text file.";
        }

        // Write wrong responses to a text file named as roll_number.txt inside ./wrong_response
        $wrongResponsesDir = './wrong_response/';
        if (!is_dir($wrongResponsesDir)) {
            mkdir($wrongResponsesDir, 0777, true); // Create directory if it doesn't exist
        }
        $wrongResponsesFileName = $wrongResponsesDir . $rollNumber . '.txt';
        $wrongResponsesFileHandle = fopen($wrongResponsesFileName, 'w'); // Open in write mode
        if ($wrongResponsesFileHandle) {
            foreach ($wrongResponses as $index => $answer) {
                fwrite($wrongResponsesFileHandle, "q" . ($index + 1) . ": " . $answer . PHP_EOL);
            }
            fclose($wrongResponsesFileHandle);
        } else {
            echo "Failed to write wrong responses to text file.";
        }

        // Redirect to welcome.php after processing the form
        header('Location: welcome.php');
        exit();
    } else {
        echo "Answers not provided.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <title>View PDF</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pdf-container, .question-container {
            width: 800px; /* Adjust width as needed */
            height: 620px; /* Adjust height as needed */
            position: fixed;
            top: 50px; /* Adjust top position as needed */
            overflow: auto; /* Enable scrolling */
            border: 1px solid #ccc; /* Add border for visibility */
        }
        .pdf-container {
            left: 20px; /* Adjust left position as needed */
        }
        .question-container {
            left: 850px; /* Adjust left position to place it beside the PDF container */
        }
        .pdf {
            width: 100%;
            height: 100%;
        }
        .question {
            margin-top: 20px;
        }
        body {
            background-color: lavender;
        }
        .main-content {
            margin-top: 50px;
        }
        .card-container {
            margin-top: 20px;
        }
        .my-button {
            margin-top: 20px;
            width: 50%;
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
    <div class="pdf-container">
        <embed class="pdf" src="<?php echo $selectedPdf; ?>" type="application/pdf">
    </div>

    <div class="question-container">
        <?php if (!empty($answers)): ?>
        <form method="post">
            <?php
            // Define options for each question
            $options = array('a', 'b', 'c', 'd');

            // Loop through each question in the text file and create radio buttons for options
            foreach ($answers as $index => $answer) {
                $questionNumber = $index + 1;
                echo '<div class="question">';
                echo '<label for="question'.$questionNumber.'">Question '.$questionNumber.':</label>';

                // Create radio buttons for options using Bootstrap's form-check class
                foreach ($options as $option) {
                    echo '<div class="form-check">';
                    echo '<input class="form-check-input" type="radio" id="question'.$questionNumber.'_'.$option.'" name="answers['.$index.']" value="'.$option.'">';
                    echo '<label class="form-check-label" for="question'.$questionNumber.'_'.$option.'">'.$option.'</label>';
                    echo '</div>';
                }
                echo '</div>';
            }
            ?>

            <!-- Use Bootstrap button class for styling -->
            <button type="submit" class="btn btn-primary my-button">Submit</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JS at the end of the body for better performance -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
