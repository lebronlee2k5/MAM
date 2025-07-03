<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = max(1, intval($_POST['quantity']));

        $stmt = $DBConnect->prepare(
            "INSERT INTO cart (user_id, product_id, quantity)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)"
        );
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
        $stmt->close();

        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['remove_id'])) {
        $remove_id = intval($_POST['remove_id']);
        $stmt = $DBConnect->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $remove_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

$stmt = $DBConnect->prepare(
    "SELECT c.id, p.name, p.price, c.quantity
     FROM cart c
     JOIN products p ON c.product_id = p.id
     WHERE c.user_id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MAM Cart - Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1 0 auto;
    }
    footer {
      flex-shrink: 0;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="index.php">
      <img src="assets/Logo.png" class="border rounded-circle" alt="" height="40">
      MAM Shop
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="allproduct.php">All Products</a></li>
      </ul>
      <form class="d-flex">
        <a href="cart.php" class="btn btn-outline-dark active">
          <i class="bi-cart-fill me-1"></i> Cart
        </a>
        <a href="logout.php" class="btn btn-outline-danger ms-3">
          <i class="bi bi-box-arrow-right"></i> Log Out
        </a>
      </form>
    </div>
  </div>
</nav>

<main class="container my-5">
  <h2 class="mb-4">Your Shopping Cart</h2>
  <?php if ($cart_items): ?>
    <?php foreach ($cart_items as $item): ?>
      <div class="cart-item border rounded p-3 mb-3 d-flex justify-content-between align-items-center">
        <div>
          <strong><?= htmlspecialchars($item['name']) ?></strong><br>
          ₱<?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?>
        </div>
        <form method="post" class="mb-0">
          <input type="hidden" name="remove_id" value="<?= $item['id'] ?>">
          <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
        </form>
      </div>
    <?php endforeach; ?>
    <div class="text-end">
      <h4>Total: ₱<?= number_format($total_amount, 2) ?></h4>
      <a href="confirm_checkout.php" class="btn btn-success">
        <i class="bi bi-cash-stack"></i> Proceed to Checkout
      </a>
    </div>
  <?php else: ?>
    <p>Your cart is empty.</p>
    <a href="allproduct.php">Back to all products</a>
  <?php endif; ?>
</main>

<footer class="py-5 bg-dark text-light text-center">
  <div class="container">
    <p class="m-0">&copy; MAM Shop Website 2025</p>
    <p class="m-0">Creator: Lebron Setosta & Cyrus Songodanan</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
