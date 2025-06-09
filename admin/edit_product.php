<?php include '../db.php';
if (!isLoggedIn() || !isAdmin()) redirect('login.php');
if (!isset($_GET['id'])) redirect('dashboard.php');

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) redirect('dashboard.php');

$errors = [];
$name = $product['name'];
$desc = $product['description'];
$price = $product['price'];
$image = $product['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = trim($_POST['name']);
    $d = trim($_POST['description']);
    $p = trim($_POST['price']);
    $img = trim($_POST['image_url']);
    $errors = [];

    if (!$n) $errors[] = "Name can't be empty";
    if (!$d) $errors[] = "Description is required";
    if (!is_numeric($p) || $p <= 0) $errors[] = "Price must be a positive number";

    if ($img) {
        if (!filter_var($img, FILTER_VALIDATE_URL)) {
            $errors[] = "Invalid image URL";
        } else {
            $image = $img;
        }
    }

    if (!$errors) {
        $u = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
        if ($u->execute([$n, $d, $p, $image, $id])) {
            $_SESSION['success_message'] = "Product updated!";
            redirect('dashboard.php');
        } else {
            $errors[] = "Update failed, please try again";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product • Admin</title>
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
        }

        .error-list li {
            margin-bottom: 6px;
        }

        .img-preview {
            margin-top: 10px;
            max-width: 100%;
            border-radius: var(--radius);
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
    Admin Panel
</nav>

<main>
    <div class="header">
        <h1>Edit Product</h1>
    </div>

    <?php if ($errors): ?>
        <div class="card error-list">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="card">
        <div class="form-group">
            <label for="name">Name</label>
            <input name="name" id="name" type="text" value="<?= htmlspecialchars($name) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($desc) ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price (₹)</label>
            <input name="price" id="price" type="number" step="0.01" value="<?= htmlspecialchars($price) ?>" required>
        </div>

        <div class="form-group">
            <label for="image_url">Image URL</label>
            <input name="image_url" id="image_url" type="url" value="<?= htmlspecialchars($image) ?>">
            <img src="<?= htmlspecialchars($image) ?>" alt="Preview" class="img-preview" id="preview">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-save">Save</button>
            <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</main>

<script>
document.getElementById('image_url').addEventListener('input', function(e){
    document.getElementById('preview').src = e.target.value;
});
</script>

</body>
</html>
