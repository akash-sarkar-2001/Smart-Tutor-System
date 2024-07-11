<?php
session_name("admin_session");
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
    $admin_username = mysqli_real_escape_string($conn, $_POST['admin_username']);
    $admin_password = mysqli_real_escape_string($conn, $_POST['admin_password']);

    // Verify the login credentials
    $stmt = $conn->prepare("SELECT * FROM admin_login WHERE admin_username = ? AND admin_password = ?");
    $stmt->bind_param("ss", $admin_username, $admin_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['admin_username'] = $admin_username;
        header("Location: welcome_admin.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
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
        <h2>Admin Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="admin_username">Username:</label>
                <input type="text" id="admin_username" name="admin_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_password">Password:</label>
                <input type="password" id="admin_password" name="admin_password" class="form-control" required>
            </div>
            <button type="submit" value="Login" class="btn btn-primary">Login</button>
            <a href="admin_register.php" class="btn btn-secondary">Register?</a>
        </form>
    </div>
</body>
</html>
