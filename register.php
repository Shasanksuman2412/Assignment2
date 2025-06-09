<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Create Account - Product System</title>
<style>
   
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    color: #333;
}

.container {
    background: #fff;
    border-radius: 14px;
    width: 360px;
    padding: 40px 36px;
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.25);
    text-align: center;
    transition: box-shadow 0.3s ease;
}

.container:hover {
    box-shadow: 0 18px 40px rgba(118, 75, 162, 0.35);
}

h2 {
    font-weight: 700;
    font-size: 28px;
    margin-bottom: 30px;
    color: #4b3f72;
    letter-spacing: 1px;
}

.form-group {
    margin-bottom: 22px;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 15px;
    color: #5a4a8a;
    user-select: none;
}

input {
    width: 100%;
    padding: 14px 16px;
    font-size: 16px;
    border: 2px solid #d1c4e9;
    border-radius: 8px;
    outline-offset: 3px;
    font-weight: 500;
    color: #3e2c7f;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input::placeholder {
    color: #b3a3d1;
    font-style: italic;
}

input:focus {
    border-color: #764ba2;
    box-shadow: 0 0 8px rgba(118, 75, 162, 0.5);
}

.btn {
    width: 100%;
    padding: 14px 0;
    background: #764ba2;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    cursor: pointer;
    letter-spacing: 1.2px;
    box-shadow: 0 6px 16px rgba(118, 75, 162, 0.5);
    transition: background-color 0.3s ease, transform 0.15s ease;
}

.btn:hover {
    background: #5a357a;
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(90, 53, 122, 0.7);
}

.link {
    margin-top: 25px;
    font-size: 15px;
    color: #5a4a8a;
    user-select: none;
}

.link a {
    color: #764ba2;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
}

.link a:hover {
    text-decoration: underline;
    color: #5a357a;
}

.error, .success {
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: 600;
    user-select: none;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.error {
    background: #ffdddd;
    color: #d32f2f;
    border: 1.5px solid #d32f2f;
}

.success {
    background: #e6f4ea;
    color: #388e3c;
    border: 1.5px solid #388e3c;
}

</style>
</head>
<body>
<div class="container">
<h2>Create an Account</h2>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    if (!$username || !$email || !$password || !$confirm_password) {
        $errors[] = "All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $checkStmt->execute([$username, $email]);
    if ($checkStmt->fetch()) {
        $errors[] = "Username or email already exists";
    }

    if (!$errors) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if ($insertStmt->execute([$username, $email, $hashed])) {
            echo '<div class="success">Registration successful! <a href="login.php">Login here</a></div>';
            $username = $email = '';
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }

    if ($errors) {
        echo '<div class="error">' . implode('<br>', $errors) . '</div>';
    }
}
?>


<form method="POST" action="">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit" class="btn">Register</button>
</form>

<div class="link">
    Already have an account? <a href="login.php">Login here</a>
</div>
</div>
</body>
</html>
