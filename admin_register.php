<?php
// PHP code for database connection and form submission

// Define your predefined key
$predefinedKey = "SET-NU@2024";

// Establish database connection
$conn = mysqli_connect("localhost", "root", "", "sts");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission for registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['admin_dept'])) {
    // Escape user inputs for security
    $admin_name = mysqli_real_escape_string($conn, $_POST['admin_name']);
    $admin_username = mysqli_real_escape_string($conn, $_POST['admin_username']);
    $admin_password = mysqli_real_escape_string($conn, $_POST['admin_password']);
    $admin_dept = mysqli_real_escape_string($conn, $_POST['admin_dept']);
    $predefined_key = mysqli_real_escape_string($conn, $_POST['predefined_key']);

    // Convert selected subjects array into a comma-separated string
    $admin_subjects = isset($_POST['admin_subjects']) ? implode(",", $_POST['admin_subjects']) : '';

    // Check if the predefined key matches
    if ($predefined_key === $predefinedKey) {
        // Insert data into admin_register table
        $sql_register = "INSERT INTO admin_register (admin_name, admin_username, admin_password, admin_dept, admin_subjects) 
                VALUES ('$admin_name', '$admin_username', '$admin_password', '$admin_dept', '$admin_subjects')";

        // Insert data into admin_login table
        $sql_login = "INSERT INTO admin_login (admin_username, admin_password) VALUES ('$admin_username', '$admin_password')";

        if (mysqli_query($conn, $sql_register) && mysqli_query($conn, $sql_login)) {
            echo "<script>window.location.href = 'admin_login.php';</script>";
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid predefined key!";
    }
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration Page</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: lavender;
            margin: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        $(document).ready(function(){
            $('#admin_dept').change(function(){
                var dept = $(this).val();
                if (dept) {
                    $.ajax({
                        type: 'POST',
                        url: 'fetch_subjects_admin.php', // Separate file for AJAX request
                        data: {dept: dept},
                        success: function(response){
                            $('#subjectList').html(response);
                        }
                    });
                } else {
                    $('#subjectList').html('<p>Please select a department</p>');
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Admin Registration Form</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="admin_name">Name:</label>
                <input type="text" class="form-control" id="admin_name" name="admin_name" required>
            </div>

            <div class="form-group">
                <label for="admin_username">Username:</label>
                <input type="text" class="form-control" id="admin_username" name="admin_username" required>
            </div>

            <div class="form-group">
                <label for="admin_password">Password:</label>
                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
            </div>

            <div class="form-group">
                <label for="admin_dept">Department:</label>
                <select class="form-control" id="admin_dept" name="admin_dept" required>
                    <option value="">Select Department</option>
                    <?php
                    $conn = mysqli_connect("localhost", "root", "", "sts");

                    // Fetch distinct departments from sub_list
                    $query = "SELECT DISTINCT dept FROM sub_list";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_array($result)) {
                        echo '<option value="'.$row['dept'].'">'.$row['dept'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="admin_subjects">Select Subjects:</label>
                <div id="subjectList">
                    <!-- Subjects will be dynamically populated here based on selected department -->
                </div>
            </div>

            <!-- Text input field for the predefined key -->
            <div class="form-group">
                <label for="predefined_key">Key:</label>
                <input type="password" class="form-control" id="predefined_key" name="predefined_key" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="admin_login.php" class="btn btn-secondary">Login?</a>
        </form>
    </div>

    <?php
    // PHP code for database connection and form submission
    ?>
</body>
</html>
