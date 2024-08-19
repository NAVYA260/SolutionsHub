<?php
session_start();
include 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize error message variable
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate form inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if the username or email already exists
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? OR email = ?");
        if (!$stmt) {
            die("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if (!$stmt) {
                die("Failed to prepare statement: " . $conn->error);
            }
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                // Redirect to login page with success message
                $_SESSION['success_message'] = "Registration successful! You can now log in.";
                header("Location: login.php");
                exit();
            } else {
                $error_message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(216, 206, 206);
        }
        form {
            width: 300px;
            text-align: center;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"]{
            padding-left: 10px;
            width: calc(100% - 10px); /* Adjusted for better fit */
            margin-bottom: 10px;
            height: 40px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button[type="submit"] {
            background-color: rgb(45,136,164);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }
        .forgot-password {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
        }
        .remember-me {
            text-align: left;
            margin-bottom: 10px;
        }
        .create-account a {
            display: block;
            text-decoration: none;
        }
        .btn-continue {
            background-color: rgb(45, 136, 164);
            color: #fff;
            padding: 10px 20px; /* Adjusted padding */
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-decoration: none;
            width: 86%; /* Ensure full width */
            margin-top: 20px;
            display: inline-block; /* Ensure it's inline with the text */
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form action="register.php" method="post">
        <h1>Register</h1>
        <div>
            <input type="text" id="username" name="username" placeholder="Username" required>
        </div>
        <div>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
        <div>
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <div>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <button type="submit">Register</button>
        <?php
        if (!empty($error_message)) {
            echo "<div class='error-message'>" . htmlspecialchars($error_message) . "</div>";
        }
        ?>
    </form>
</body>
</html>
