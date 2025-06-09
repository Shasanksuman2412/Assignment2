<?php
include 'db.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
    }

    $_SESSION['cart_message'] = "Cart updated successfully!";
    redirect('cart.php');
}

if (isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];

    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);

    $_SESSION['cart_message'] = "Item removed from cart!";
    redirect('cart.php');
}

$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.description, p.price, p.image
    FROM cart c
    INNER JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$stmt = $pdo->prepare("SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$result = $stmt->fetch();
$cart_count = $result['total_quantity'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Cart - Simple Store</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
        color: #333;
    }
    header {
        background-color:rgb(5, 87, 46);
        padding: 15px 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header .logo {
        font-size: 1.5rem;
        font-weight: bold;
    }
    header nav a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
        font-weight: 600;
    }
    header nav a:hover {
        text-decoration: underline;
    }
    main {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 20px;
        background: white;
        box-shadow: 0 0 5px #ccc;
        border-radius: 4px;
    }
    h1 {
        text-align: center;
        margin-bottom: 25px;
    }
    .alert {
        background-color: #d4edda;
        color: #155724;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    thead {
        background-color:rgb(3, 85, 51);
        color: white;
    }
    th, td {
        padding: 12px 10px;
        border: 1px solid #ddd;
        text-align: left;
        vertical-align: middle;
    }
    tbody tr:nth-child(even) {
        background-color: #f5f5f5;
    }
    .product-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 3px;
    }
    .product-name {
        font-weight: 600;
    }
    form.quantity-form input[type=number] {
        width: 50px;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 3px;
        text-align: center;
        font-size: 1rem;
    }
    form.quantity-form button,
    form.remove-form button {
        background-color: #2980b9;
        color: white;
        border: none;
        padding: 7px 14px;
        border-radius: 3px;
        cursor: pointer;
        font-weight: 600;
    }
    form.quantity-form button:hover,
    form.remove-form button:hover {
        background-color: #1c5980;
    }
    form.remove-form button {
        background-color: #c0392b;
    }
    form.remove-form button:hover {
        background-color: #7a1a17;
    }
    .total-section {
        margin-top: 25px;
        text-align: right;
        font-size: 1.2rem;
        font-weight: 700;
    }
    .empty-cart {
        text-align: center;
        padding: 60px 20px;
        color: #777;
    }
    .empty-cart a {
        color: #2980b9;
        text-decoration: none;
        font-weight: 600;
    }
    .empty-cart a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<header>
    <div class="logo">Simple Store</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="cart.php">Cart </a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="alert"><?= $_SESSION['cart_message'] ?></div>
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>

    <h1>Your Cart</h1>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is currently empty.</p>
            <p><a href="index.php">Continue shopping &rarr;</a></p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Subtotal (₹)</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-img" />
                            <div class="product-name"><?= htmlspecialchars($item['name']) ?></div>
                        </td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td>
                            <form method="POST" class="quantity-form" action="">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" />
                                <button type="submit" name="update_quantity">Update</button>
                            </form>
                        </td>
                        <td><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        <td>
                            <form method="POST" class="remove-form" action="">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit" name="remove_item">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            Total: ₹<?= number_format($total, 2) ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
