<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Access Portal</title>
    <style>
       
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #333;
    padding: 20px;
}

.form-box {
    background: #ffffffdd;
    backdrop-filter: blur(8px);
    border-radius: 12px;
    padding: 40px 36px;
    width: 360px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    text-align: center;
    transition: box-shadow 0.3s ease;
}

.form-box:hover {
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
}

.form-box h1 {
    font-size: 28px;
    margin-bottom: 28px;
    color: #2c3e50;
    letter-spacing: 1px;
    font-weight: 700;
}

.input-set {
    margin-bottom: 22px;
    text-align: left;
}

.input-set label {
    display: block;
    font-size: 15px;
    color: #34495e;
    margin-bottom: 8px;
    font-weight: 600;
}

.input-set input {
    width: 100%;
    padding: 14px 16px;
    font-size: 16px;
    border: 2px solid #bdc3c7;
    border-radius: 8px;
    outline-offset: 2px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-weight: 500;
    color: #2c3e50;
}

.input-set input::placeholder {
    color: #95a5a6;
}

.input-set input:focus {
    border-color: #2575fc;
    box-shadow: 0 0 8px rgba(37, 117, 252, 0.5);
}

.submit-btn {
    background: #2575fc;
    color: #fff;
    width: 100%;
    padding: 14px 0;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.15s ease;
    box-shadow: 0 5px 15px rgba(37, 117, 252, 0.4);
}

.submit-btn:hover {
    background: #1a52d1;
    transform: scale(1.05);
    box-shadow: 0 7px 20px rgba(26, 82, 209, 0.6);
}

.notice {
    background: #ff4d4f;
    color: white;
    padding: 12px 16px;
    border-radius: 6px;
    font-weight: 600;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(255, 77, 79, 0.4);
}

.account-link {
    margin-top: 20px;
    font-size: 15px;
    color: #34495e;
}

.account-link a {
    color: #2575fc;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
}

.account-link a:hover {
    text-decoration: underline;
    color: #1a52d1;
}

    </style>
</head>
<body>
    <div class="form-box">
        <h1>Welcome Back</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputName = trim($_POST['username']);
            $inputPass = $_POST['password'];

            $query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $query->execute([$inputName]);
            $record = $query->fetch();

            if ($record && password_verify($inputPass, $record['password_hash'])) {
                $_SESSION['user_id'] = $record['user_id'];  // Make sure your DB column is user_id
                $_SESSION['username'] = $record['username'];
                $_SESSION['is_admin'] = $record['is_admin'];

                $destination = $record['is_admin'] ? 'admin/dashboard.php' : 'index.php';
                redirect($destination);
            } else {
                echo '<div class="notice">Incorrect login credentials</div>';
            }
        }
        ?>

        <form method="POST" action="">
            <div class="input-set">
                <label for="username">User ID</label>
                <input type="text" name="username" id="username" required autocomplete="username" />
            </div>
            <div class="input-set">
                <label for="password">Secret Key</label>
                <input type="password" name="password" id="password" required autocomplete="current-password" />
            </div>
            <button class="submit-btn" type="submit">Sign In</button>
        </form>

        <div class="account-link">
            New user? <a href="register.php">Create account</a>
        </div>
    </div>
</body>
</html>
