<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(216, 206, 206);
        }
        .navbar {
            background-color: rgb(45, 136, 164);
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            font-size: 24px;
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
        .navbar-options a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">SolutionsHub</div>
        <input type="text" class="search-bar" placeholder="Search...">
        <div class="navbar-options">
            <a href="index.html">Home</a>
            <a href="subjects.php">Subjects</a>
            <a href="profile.php">Profile</a>
        </div>
    </div>
</body>
</html>
