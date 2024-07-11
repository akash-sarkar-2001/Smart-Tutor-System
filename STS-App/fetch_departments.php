<?php
// fetch_departments.php

// Establish database connection
$conn = mysqli_connect("localhost", "root", "", "sts");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch departments based on selected semester
if(isset($_POST['semester'])) {
    $selected_semester = $_POST['semester'];

    $query = "SELECT DISTINCT dept FROM sub_list WHERE sem = $selected_semester";
    $result = mysqli_query($conn, $query);

    // Generate dropdown options for departments
    echo '<option value="">Select Department</option>'; // Placeholder option
    while ($row = mysqli_fetch_array($result)) {
        echo '<option value="'.$row['dept'].'">'.$row['dept'].'</option>';
    }
}

// Close database connection
mysqli_close($conn);
?>
