<?php
session_start();
include 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$error_message = '';
$success_message = '';

// Retrieve the topic_name and subject_name from the URL
$topic_name = isset($_GET['topic_name']) ? $_GET['topic_name'] : '';
$subject_name = isset($_GET['subject_name']) ? $_GET['subject_name'] : '';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $error_message = "You must be logged in to submit a solution.";
} else {
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $solution_content = $_POST['solutionContent'];
        $username = $_SESSION['username'];

        if (!empty($topic_name)) {
            // Check if the topic_name is valid
            $topic_check = $conn->prepare("SELECT topic_name FROM topics WHERE topic_name = ?");
            $topic_check->bind_param("s", $topic_name);
            $topic_check->execute();
            $topic_check->store_result();

            if ($topic_check->num_rows > 0) {
                // Check if the username exists
                $user_check = $conn->prepare("SELECT username FROM users WHERE username = ?");
                $user_check->bind_param("s", $username);
                $user_check->execute();
                $user_check->store_result();

                if ($user_check->num_rows > 0) {
                    // Prepare and execute SQL statement to insert new solution
                    $stmt = $conn->prepare("INSERT INTO solutions (content, topic_name, username) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $solution_content, $topic_name, $username);

                    if ($stmt->execute()) {
                        $success_message = "Solution submitted successfully!";
                    } else {
                        $error_message = "Error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $error_message = "Error: User does not exist.";
                }

                $user_check->close();
            } else {
                $error_message = "Error: Invalid topic name.";
            }

            $topic_check->close();
        } else {
            $error_message = "Error: Topic name is missing or invalid.";
        }
    }
}

// Fetch solutions for the given topic_name in reverse chronological order
$solutions = [];
if (!empty($topic_name)) {
    $stmt = $conn->prepare("SELECT content, username FROM solutions WHERE topic_name = ? ORDER BY id DESC");
    $stmt->bind_param("s", $topic_name);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $solutions[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Solution</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: rgb(216, 206, 206);
        }

        .navbar {
            background-color: rgb(45, 136, 164);
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 24px;
        }

        .navbar-options {
            display: flex;
            align-items: center;
        }

        .navbar-options a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-size: 18px;
        }

        .search-bar {
            width: 300px;
            padding: 8px;
            border-radius: 5px;
            border: none;
        }

        .profile-logo {
            background-color: #444;
            color: #fff;
            padding: 8px 15px;
            border-radius: 50%;
            cursor: pointer;
        }

        .solutions-list {
            padding: 20px;
        }

        .solution-card {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
        }

        .solution-card .username {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 14px;
            color: #555;
        }

        .solution-form {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            margin: 20px;
        }

        .solution-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            resize: vertical;
        }

        .solution-form input[type="submit"] {
            background-color: rgb(45, 136, 164);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="solutions-list">
        <h2>Solutions for Topic: <?php echo htmlspecialchars($topic_name); ?></h2>
        <?php if (!empty($solutions)): ?>
            <?php foreach ($solutions as $solution): ?>
                <div class="solution-card">
                    <p class="username"><?php echo htmlspecialchars($solution['username']); ?></p>
                    <p><?php echo htmlspecialchars($solution['content']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No solutions available for this topic.</p>
        <?php endif; ?>
    </div>
    <div class="solution-form">
        <h2>Submit a New Solution</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <form action="solutions.php?topic_name=<?php echo htmlspecialchars($topic_name); ?>&subject_name=<?php echo htmlspecialchars($subject_name); ?>" method="post">
            <label for="solutionContent">Solution:</label>
            <textarea id="solutionContent" name="solutionContent" rows="4" placeholder="Enter your solution here..." required></textarea><br>
            <input type="hidden" name="topic_name" value="<?php echo htmlspecialchars($topic_name); ?>">
            <input type="submit" value="Submit Solution">
        </form>
    </div>
</body>
</html>
