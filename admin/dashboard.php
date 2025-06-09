<?php include '../db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Ecommerce System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary:rgb(34, 110, 86);
            --secondary:rgb(54, 105, 86);
            --accent: #2a9d8f;
            --bg-light: #f1f1f1;
            --white: #ffffff;
            --danger: #e63946;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font);
        }

        body {
            background-color: var(--bg-light);
            color: var(--primary);
        }

        header {
            background-color: var(--primary);
            padding: 20px;
            color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
        }

        .nav-links a {
            margin-left: 20px;
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }

        .title {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--secondary);
        }

        .card-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .card {
            background: var(--white);
            flex: 1;
            min-width: 200px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.07);
        }

        .card h3 {
            font-size: 16px;
            color: #888;
            margin-bottom: 10px;
        }

        .card .number {
            font-size: 24px;
            font-weight: bold;
            color: var(--accent);
        }

        .section-title {
            font-size: 22px;
            margin: 30px 0 15px;
            border-left: 5px solid var(--secondary);
            padding-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: var(--secondary);
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--secondary);
        }

        .btn-primary:hover {
            background-color: #356b8b;
        }

        .btn-danger {
            background-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #c92c35;
        }

        .image-cell {
            max-width: 100px;
            word-wrap: break-word;
        }

        @media (max-width: 768px) {
            .card-grid {
                flex-direction: column;
            }

            .nav-links {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo"> Admin Panel</div>
    <div class="nav-links">
      
        <a href="add_product.php">Add Product</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="container">
    <div class="title">Admin Dashboard</div>

    <div class="card-grid">
    </div>

    <div class="section-title">Products</div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>₹<?= number_format($product['price'], 2) ?></td>
                <td class="image-cell"><?= htmlspecialchars($product['image']) ?></td>
                <td>
                    <a class="btn btn-primary" href="edit_product.php?id=<?= $product['id'] ?>">Edit</a>
                    <a class="btn btn-danger" href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
