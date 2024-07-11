<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body {
            background-color: lavender;
            color: #000; /* Change text color to black for better contrast */
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 100px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Registration Form</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="fname">Name:</label>
                <input type="text" id="fname" name="fname" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="rollno">Roll Number:</label>
                <input type="text" id="rollno" name="rollno" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="semester">Semester:</label>
                <select id="semester" name="semester" class="form-control" required>
                    <option value="">Select Semester</option>
                    <?php
                    $conn = mysqli_connect("localhost", "root", "", "sts");

                    // Fetch distinct semesters from sub_list
                    $query = "SELECT DISTINCT sem FROM sub_list";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_array($result)) {
                        echo '<option value="'.$row['sem'].'">'.$row['sem'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="department">Department:</label>
                <select id="department" name="department" class="form-control" required>
                    <option value="">Select Department</option>
                </select>
            </div>

            <div class="form-group">
                <label for="subjects">Select Subjects:</label>
                <div id="subjectList"></div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="login.php" class="btn btn-secondary">Have an account?</a>
        </form>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // jQuery script to fetch departments based on selected semester
        $(document).ready(function(){
            $('#semester').change(function(){
                var semester = $(this).val();

                $.ajax({
                    url: 'fetch_departments.php',
                    method: 'post',
                    data: {semester: semester},
                    success: function(response){
                        $('#department').html(response);
                    }
                });
            });
        });

        // jQuery script to fetch subjects based on selected semester and department
        $(document).ready(function(){
            $('#semester, #department').change(function(){
                var semester = $('#semester').val();
                var department = $('#department').val();

                $.ajax({
                    url: 'fetch_subjects.php',
                    method: 'post',
                    data: {semester: semester, department: department},
                    success: function(response){
                        $('#subjectList').html(response);
                    }
                });
            });
        });
    </script>

    <?php
    // Establish database connection
    $conn = mysqli_connect("localhost", "root", "", "sts");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Escape user inputs for security
        $fname = mysqli_real_escape_string($conn, $_POST['fname']);
        $rollno = mysqli_real_escape_string($conn, $_POST['rollno']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $semester = mysqli_real_escape_string($conn, $_POST['semester']);
        $department = mysqli_real_escape_string($conn, $_POST['department']);

        // Convert selected subjects array into a comma-separated string
        $subjects = implode(",", $_POST['subjects']);

        // Insert data into register table
        $sql_register = "INSERT INTO register (fname, rollno, email, password_, sem, dept, selected_subject) 
                VALUES ('$fname', '$rollno', '$email', '$password', '$semester', '$department', '$subjects')";

        // Insert data into login_ table
        $sql_login = "INSERT INTO login_ (rollno, password_) VALUES ('$rollno', '$password')";

        if (mysqli_query($conn, $sql_register) && mysqli_query($conn, $sql_login)) {
            header("Location: login.php");
            exit();
        } else {
            echo '<div class="alert alert-danger mt-3" role="alert">Error: ' . mysqli_error($conn) . '</div>';
        }
    }

    // Close database connection
    mysqli_close($conn);
    ?>
</body>
</html>
