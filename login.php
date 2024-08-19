<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        if (password_verify($password, $stored_password)) {
            $_SESSION['username'] = $username;
            header("Location: subjects.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "No user found with that username.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        input[type="password"] {
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
    <form action="login.php" method="post">
        <h1>Login</h1>
        <div>
            <input type="text" id="username" name="username" placeholder="Username" required>
        </div>
        <div>
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <div class="remember-me">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember me</label>
        </div>
        <button type="submit">Login</button>
        <div class="forgot-password">
            <a href="#">Forgot password?</a>
        </div>
        <div class="create-account">
            <a href='register.php' class="btn-continue">Create an account</a>
        </div>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            // Clear the error message after displaying it
            unset($_SESSION['error']);
        }
        ?>
    </form>
</body>
</html>
