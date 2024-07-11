<?php
session_name("user_session");
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sts";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rollno = mysqli_real_escape_string($conn, $_POST['rollno']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Verify the login credentials
    $stmt = $conn->prepare("SELECT * FROM login_ WHERE rollno = ? AND password_ = ?");
    $stmt->bind_param("ss", $rollno, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['rollno'] = $rollno;
        header("Location: welcome.php");
        exit();
    } else {
        echo "Invalid roll number or password.";
    }

    $stmt->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Your custom styles here */
        body {
            background-color: lavender;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="rollno">Roll Number:</label>
                <input type="text" id="rollno" name="rollno" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" value="Login" class="btn btn-primary">Login</button>
            <a href="register.php" class="btn btn-secondary">Register?</a>
        </form>
    </div>
</body>
</html>

