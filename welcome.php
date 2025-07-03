<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $DBConnect->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname);
$stmt->fetch();
$stmt->close();

$cart_count = 0;
$stmt = $DBConnect->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_count);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    $check_stmt = $DBConnect->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $update_stmt = $DBConnect->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        $insert_stmt = $DBConnect->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $check_stmt->close();
    header("Location: welcome.php");
    exit();
}

$stmt = $DBConnect->prepare("SELECT id, name, price, rating, description, image FROM products ORDER BY rating DESC, id DESC LIMIT 5");
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
  <title>MAM Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f9fbfd; }
    .hero { position: relative; display: flex; justify-content: center; align-items: center; height: 40vh; background: url('assets/hero.jpg') no-repeat center/cover; color: white; text-align: center; }
    .hero::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
    .hero-content { position: relative; z-index: 1; }
    main { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
    .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }

    .product {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: white;
      border-radius: 8px;
      padding: 1rem;
      text-align: center;
      box-shadow: 0 0 8px rgba(0,0,0,0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .product-body {
      flex: 1 1 auto;
    }

    .product img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      margin-bottom: 0.5rem;
      border-radius: 4px;
      background: #f0f0f0;
    }

    .stars { color: gold; font-size: 1rem; }

    .product form {
      margin-top: auto;
    }

    .input-group input[type="number"] {
      max-width: 70px;
      text-align: center;
    }

    .input-group .btn {
      white-space: nowrap;
    }
    .welcome_greeting{
      font-family:Comfortaa;
      font-size: 4em;
    }

  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="index.php" style="font-family:Georgia;">
      <img src="assets/Logo.png" class="border rounded-circle" alt="" height="40">
      MAM Shop
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link active border-bottom border-3 border-dark fw-bold" href="welcome.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="allproduct.php">All Products</a></li>
      </ul>

      <form class="d-flex">
        <a href="cart.php" class="btn btn-outline-dark">
          <i class="bi-cart-fill me-1"></i> Cart
          <span class="badge bg-dark text-white ms-1 rounded-pill"><?= $cart_count ?></span>
        </a>
        <a href="logout.php" class="btn btn-outline-danger ms-3">
          <i class="bi bi-box-arrow-right"></i> Log Out
        </a>
      </form>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="hero-content">
    <h1 class="welcome_greeting">Welcome, <?= htmlspecialchars($fullname) ?>!</h1>
    <p style="font-size: 22px;">Enjoy your Shopping with MAM Shop, Everything you need is Here.</p>
  </div>
</section>

<main>
  <h2 class="text-center mb-4">Top 5 Recommended Products</h2>
  <div class="products-grid">
    <?php if ($products): ?>
      <?php foreach ($products as $product): ?>
        <div class="product">
          <div class="product-body">
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
          </div>

          <form method="post" action="welcome.php" class="mt-3">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <div class="input-group input-group-sm">
              <input type="number" name="quantity" value="1" min="1" class="form-control text-center">
              <button type="submit" class="btn btn-success">
                <i class="bi bi-cart-plus"></i> Add to Cart
              </button>
            </div>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No products available at the moment.</p>
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
