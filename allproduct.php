<?php
session_start();
include("db_connect.php");

$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user_id) {
        header("Location: login.php");
        exit();
    }

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

        header("Location: allproduct.php");
        exit();
    }
}

$cart_count = 0;
if ($user_id) {
    $stmt = $DBConnect->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($cart_count);
    $stmt->fetch();
    $stmt->close();
}

$sort = $_GET['sort'] ?? 'recommended';
$order_by = "(rating / 2) DESC";
if ($sort === 'cheapest') {
    $order_by = "price ASC";
} elseif ($sort === 'highest') {
    $order_by = "price DESC";
}

$stmt = $DBConnect->prepare("SELECT id, name, price, rating, description, image FROM products ORDER BY $order_by");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MAM Shop - All Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f9fbfd; }
    .hero { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 350px; background: url('assets/hero.jpg') no-repeat center center/cover; color: white; text-align: center; }
    .hero::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
    .hero-content { position: relative; z-index: 1; }
    main { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
    .sort-form { text-align: right; margin-bottom: 1.5rem; }
    .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
    .product { display: flex; flex-direction: column; justify-content: space-between; background: white; border-radius: 8px; padding: 1rem; text-align: center; box-shadow: 0 0 8px rgba(0,0,0,0.08); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .product:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .product img { max-width: 100%; height: 150px; object-fit: cover; margin-bottom: 0.5rem; border-radius: 4px; background: #f0f0f0; }
    .product .stars { color: gold; font-size: 1rem; }
    .input-group input[type="number"] { max-width: 70px; text-align: center; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="index.php" style="font-family: Georgia;">
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
        <li class="nav-item"><a class="nav-link active border-bottom border-3 border-dark fw-bold" href="allproduct.php">All Products</a></li>
      </ul>
      <form class="d-flex">
  <a href="cart.php" class="btn btn-outline-dark">
    <i class="bi-cart-fill me-1"></i> Cart
    <span class="badge bg-dark text-white ms-1 rounded-pill"><?= $cart_count ?></span>
  </a>

  <?php if (!$user_id): ?>
    <a href="login.php" class="btn btn-outline-dark ms-3">
      <i class="bi bi-person-circle"></i> Log&nbsp;In
    </a>
  <?php else: ?>
    <a href="logout.php" class="btn btn-outline-danger ms-3">
      <i class="bi bi-box-arrow-right"></i> Log&nbsp;Out
    </a>
  <?php endif; ?>
</form>

    </div>
  </div>
</nav>

<section class="hero">
  <div class="hero-content">
    <h1 style="font-family:Comfortaa; font-size:4em;">Explore All Products</h1>
  </div>
</section>

<main>
  <form method="get" class="sort-form">
    <label class="fw-bold">Sort by:</label>
    <select name="sort" onchange="this.form.submit()">
      <option value="recommended" <?= $sort === 'recommended' ? 'selected' : '' ?>>Recommended</option>
      <option value="cheapest" <?= $sort === 'cheapest' ? 'selected' : '' ?>>Cheapest</option>
      <option value="highest" <?= $sort === 'highest' ? 'selected' : '' ?>>Highest Price</option>
    </select>
  </form>

  <div class="products-grid">
    <?php if ($products): ?>
      <?php foreach ($products as $product): ?>
        <div class="product">
          <img src="assets/<?= htmlspecialchars($product['image'] ?: 'product_placeholder.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="border border-muted">
          <h3><?= htmlspecialchars($product['name']) ?></h3>
          <p>₱<?= number_format($product['price'], 2) ?></p>
          <p class="stars">
            <?php
              $stars = min(5, floor($product['rating'] / 2));
              echo str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
            ?>
          </p>
          <p><?= htmlspecialchars($product['description']) ?></p>

          <form method="post" class="mt-3">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <div class="input-group input-group-sm">
              <input type="number" name="quantity" value="1" min="1" class="form-control text-center" style="max-width: 70px;">
              <button type="submit" class="btn btn-success">
                <i class="bi bi-cart-plus"></i> Add to Cart
              </button>
            </div>
          </form>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center;">No products available at the moment.</p>
    <?php endif; ?>
  </div>
</main>

<footer class="py-5 bg-dark">
  <div class="container text-center text-light">
    <p class="m-0">&copy; MAM Shop Website 2025</p>
    <p class="m-0">Creator: Lebron Setosta & Cyrus Songodanan</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
