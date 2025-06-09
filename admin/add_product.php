<?php include '../db.php'; 

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$errors = [];
$name = $description = $price = $image = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $image = trim($_POST['image_url']);

    if (empty($name)) $errors[] = "Product name is required";
    if (empty($description)) $errors[] = "Description is required";
    if (empty($price) || !is_numeric($price) || $price <= 0) $errors[] = "Valid price is required";

    if (empty($image)) {
        $errors[] = "Image URL is required";
    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid image URL format";
    } else {
        $ext = strtolower(pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Only JPG, JPEG, PNG, GIF images allowed";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $price, $image])) {
            $_SESSION['success_message'] = "Product added successfully!";
            redirect('dashboard.php');
        } else {
            $errors[] = "Failed to add product";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product • Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --bg: #f4f6f9;
            --surface: #fff;
            --primary: #264653;
            --secondary: #2a9d8f;
            --error: #e76f51;
            --text: #333;
            --radius: 8px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: var(--bg); }

        nav {
            background: var(--primary);
            color: #fff;
            padding: 15px 30px;
            font-size: 18px;
            display: flex;
            gap: 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
        }
        nav a:hover {
            text-decoration: underline;
        }

        main {
            padding: 40px;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            color: var(--primary);
            font-size: 28px;
        }

        .card {
            background: var(--surface);
            padding: 30px;
            border-radius: var(--radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--text);
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: var(--radius);
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.2);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius);
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-save {
            background: var(--secondary);
        }

        .btn-save:hover {
            background: #21867a;
        }

        .btn-cancel {
            background: #aaa;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: #888;
        }

        .error-list {
            margin-bottom: 20px;
            color: var(--error);
            list-style-type: none;
        }

        .error-list li {
            margin-bottom: 6px;
        }

        .img-preview {
            margin-top: 10px;
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--radius);
            display: none;
        }

        @media(max-width:768px) {
            nav {
                font-size: 16px;
                padding: 12px 20px;
            }

            main {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<nav>
    <a href="dashboard.php">Admin Panel</a>

</nav>

<main>
    <div class="header">
        <h1>Add New Product</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="card">
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="card">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($description) ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price (₹)</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($price) ?>" required>
        </div>

        <div class="form-group">
            <label for="image_url">Image URL</label>
            <input type="url" id="image_url" name="image_url" value="<?= htmlspecialchars($image) ?>" placeholder="https://example.com/image.jpg" required>
            <img id="preview" class="img-preview" src="#" alt="Preview">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-save">Add Product</button>
            <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</main>

<script>
   
    const imageUrlInput = document.getElementById('image_url');
    const imagePreview = document.getElementById('preview');

    imageUrlInput.addEventListener('input', function () {
        const url = this.value.trim();
        if (url.match(/\.(jpeg|jpg|png|gif)$/i)) {
            imagePreview.src = url;
            imagePreview.style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
        }
    });

  
    window.addEventListener('DOMContentLoaded', () => {
        if (imageUrlInput.value.trim()) {
            imageUrlInput.dispatchEvent(new Event('input'));
        }
    });
</script>

</body>
</html>