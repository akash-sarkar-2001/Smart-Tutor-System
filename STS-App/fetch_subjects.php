<?php
// fetch_subjects.php

// Establish database connection
$conn = mysqli_connect("localhost", "root", "", "sts");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch subjects based on selected semester and department
if(isset($_POST['semester']) && isset($_POST['department'])) {
    $selected_semester = $_POST['semester'];
    $selected_department = $_POST['department'];

    $query = "SELECT subject_ FROM sub_list WHERE sem = $selected_semester AND dept = '$selected_department'";
    $result = mysqli_query($conn, $query);

    // Generate checkboxes for subjects
    while ($row = mysqli_fetch_array($result)) {
        echo '<input type="checkbox" name="subjects[]" value="'.$row['subject_'].'">'.$row['subject_'].'<br>';
    }
}

// Close database connection
mysqli_close($conn);
?>
