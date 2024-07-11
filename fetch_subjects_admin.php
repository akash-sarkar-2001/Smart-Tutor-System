<?php
// fetch_subjects_admin.php

// Establish database connection
$conn = mysqli_connect("localhost", "root", "", "sts");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['dept'])) {
    $dept = mysqli_real_escape_string($conn, $_POST['dept']);
    $query = "SELECT subject_ FROM sub_list WHERE dept = '$dept'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            echo '<input type="checkbox" name="admin_subjects[]" value="'.$row['subject_'].'">'.$row['subject_'].'<br>';
        }
    } else {
        echo '<p>No subjects found for the selected department</p>';
    }
}

// Close database connection
mysqli_close($conn);
?>
