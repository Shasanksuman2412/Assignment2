<?php
include 'db.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];

    $checkStmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$userId, $productId]);
    $cartItem = $checkStmt->fetch();

    if ($cartItem) {
        $updateStmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $updateStmt->execute([$cartItem['id']]);
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $insertStmt->execute([$userId, $productId]);
    }

    $_SESSION['cart_message'] = "Item successfully added to your cart!";
    redirect('index.php');
}

$productsStmt = $pdo->query("SELECT * FROM products");
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

$cartTotal = 0;
if (isLoggedIn()) {
    $totalStmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $totalStmt->execute([$_SESSION['user_id']]);
    $totalResult = $totalStmt->fetch();
    $cartTotal = $totalResult['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Shop Products</title>
    <style>
       body {
    font-family: Arial, sans-serif;
    background: #e7ecef;
    color: #222;
    margin: 0;
    padding: 0;
}

nav {
    background: #00695c;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #f1f8e9;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

nav .logo {
    font-size: 26px;
    font-weight: bold;
    letter-spacing: 1px;
}

nav a {
    color: #f1f8e9;
    text-decoration: none;
    margin-left: 20px;
    font-weight: 600;
    transition: color 0.3s ease;
}

nav a:hover {
    color: #a7ffeb;
}

nav .cart-count {
    background: #d32f2f;
    border-radius: 50%;
    padding: 4px 10px;
    font-size: 14px;
    vertical-align: middle;
    margin-left: 6px;
    color: white;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    min-width: 26px;
    text-align: center;
    display: inline-block;
    line-height: 1.3;
    user-select: none;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

nav .cart-count:hover {
    background: #b71c1c;
    transform: scale(1.1);
    cursor: default;
}

main {
    max-width: 1140px;
    margin: 25px auto;
    padding: 0 20px;
}

.greeting {
    font-size: 20px;
    margin-bottom: 20px;
    color: #004d40;
}

.alert {
    background-color: #c8e6c9;
    border-left: 6px solid #388e3c;
    padding: 12px 20px;
    margin-bottom: 25px;
    border-radius: 5px;
    color: #2e7d32;
}

.product-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    justify-content: center;
}

.card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
    width: 260px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
    transition: transform 0.3s ease;
}

.card:hover img {
    transform: scale(1.05);
}

.card-body {
    padding: 16px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-title {
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 10px;
    color: #00796b;
    letter-spacing: 0.02em;
}

.product-desc {
    font-size: 14px;
    color: #555;
    line-height: 1.3;
    height: 50px;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 16px;
}

.product-price {
    font-size: 20px;
    font-weight: 700;
    color: #004d40;
    margin-bottom: 14px;
}

.btn {
    background-color: #00796b;
    color: white;
    border: none;
    padding: 12px 0;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
    transition: background-color 0.3s ease;
    width: 100%;
    text-align: center;
}

.btn:hover {
    background-color: #004d40;
}

.details-btn {
    background-color: #eeeeee;
    color: #333;
    margin-top: 8px;
    font-weight: 500;
    border-radius: 6px;
    padding: 10px 0;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.details-btn:hover {
    background-color: #d6d6d6;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
    z-index: 1100;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 85vh;
    overflow-y: auto;
    padding: 30px 25px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.close-modal {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 26px;
    color: #666;
    cursor: pointer;
    font-weight: bold;
    user-select: none;
    transition: color 0.3s ease;
}

.close-modal:hover {
    color: #000;
}

.modal-image {
    width: 100%;
    height: 320px;
    object-fit: contain;
    margin-bottom: 20px;
    background: #f2f2f2;
    border-radius: 6px;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #004d40;
    margin-bottom: 12px;
}

.modal-price {
    font-size: 22px;
    font-weight: 700;
    color: #00796b;
    margin-bottom: 18px;
}

.modal-description {
    font-size: 16px;
    line-height: 1.5;
    color: #444;
    margin-bottom: 30px;
}

    </style>
</head>

<body>
    <nav>
        <div class="logo">Shopping</div>
        <div>
           <a href="cart.php">Cart</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <?php if (!empty($_SESSION['cart_message'])): ?>
            <div class="alert"><?= $_SESSION['cart_message'] ?></div>
            <?php unset($_SESSION['cart_message']); ?>
        <?php endif; ?>

        <div class="greeting">Hello, <?= htmlspecialchars($_SESSION['username']) ?>! Explore our collection:</div>

        <section class="product-grid">
            <?php foreach ($products as $item): ?>
                <article class="card">
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" />
                    <div class="card-body">
                        <h3 class="product-title"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars(mb_strimwidth($item['description'], 0, 80, '...')) ?></p>
                        <div class="product-price">₹<?= number_format($item['price'], 2) ?></div>

                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>" />
                            <button class="btn" type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                        <button class="btn details-btn" type="button" onclick='openModal(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>Details</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <img id="modalImage" class="modal-image" src="" alt="Product Image" />
            <h2 id="modalTitle" class="modal-title"></h2>
            <p id="modalPrice" class="modal-price"></p>
            <p id="modalDesc" class="modal-description"></p>
            <form method="POST" action="">
                <input type="hidden" id="modalProductId" name="product_id" value="" />
                <button class="btn" type="submit" name="add_to_cart">Add to Cart</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(product) {
            document.getElementById('modalImage').src = product.image;
            document.getElementById('modalTitle').textContent = product.name;
            document.getElementById('modalPrice').textContent = '₹' + parseFloat(product.price).toFixed(2);
            document.getElementById('modalDesc').textContent = product.description;
            document.getElementById('modalProductId').value = product.id;
            document.getElementById('productModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('productModal')) {
                closeModal();
            }
        };
    </script>
</body>

</html>