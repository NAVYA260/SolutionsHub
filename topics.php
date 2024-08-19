<?php
include 'db.php'; // Include database configuration file

// Retrieve the subject_name from the URL
$subject_name = isset($_GET['subject_name']) ? $_GET['subject_name'] : '';

// Initialize message variable
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic_name = $_POST['topic_name'];

    // Insert the new topic into the database
    $stmt = $conn->prepare("INSERT INTO topics (topic_name, subject_name) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $topic_name, $subject_name); // Use "ss" to bind two strings

        if ($stmt->execute()) {
            $message = "Topic added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Error preparing statement: " . $conn->error;
    }
}

// Fetch topics based on the subject_name
$topics = [];
$stmt = $conn->prepare("SELECT topic_name FROM topics WHERE subject_name = ?");
if ($stmt) {
    $stmt->bind_param("s", $subject_name); // Use "s" to bind a string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
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

        .questions {
            flex: 1;
            padding: 20px;
        }

        .question-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
        }

        .question-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .question-card ul li a {
            display: block;
            background-color: rgb(144, 156, 159);
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .question-card ul li a:hover {
            background-color: #555;
        }

        .question-form {
            width: 100%;
            margin-top: 20px;
        }

        .question-form input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .question-form input[type="submit"] {
            background-color: rgb(45, 136, 164);
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
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
    <div class="questions">
        <div class="question-card">
            <h2>Topics for Subject: <?php echo htmlspecialchars($subject_name); ?></h2>
            <ul>
                <?php foreach ($topics as $topic): ?>
                    <li><a href="solutions.php?topic_name=<?php echo htmlspecialchars($topic['topic_name']); ?>&subject_name=<?php echo urlencode($subject_name); ?>"><?php echo htmlspecialchars($topic['topic_name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="question-form">
            <div class="question-card">
                <h2>Add Topic</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form action="topics.php?subject_name=<?php echo urlencode($subject_name); ?>" method="post">
                    <input type="text" id="topic_name" name="topic_name" placeholder="Topic Name" required>
                    <input type="submit" value="Add Topic">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
