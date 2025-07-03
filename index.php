<?php
session_start();
include("db_connect.php");

if (isset($_SESSION['user_id'])) {
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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>MAM Shop - Home</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    color: #333;
    background: #f9fbfd;
}

.hero {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 85vh;
    background: url('assets/hero.jpg') no-repeat center center/cover;
    color: white;
    text-align: center;
}

.hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
}

.hero-content {
    position: relative;
    z-index: 1;
    padding: 2rem;
}

.hero h1 {
    font-size: 2.5rem;
    margin: 0.5rem 0;
}

.hero p {
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto 1rem auto;
}

.section-title {
    text-align: center;
    font-size: 1.8rem;
    margin: 2rem 0 1rem 0;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto 2rem auto;
    padding: 0 1rem;
}

.product {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    box-shadow: 0 0 8px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product img {
    max-width: 100%;
    height: 150px;
    object-fit: cover;
    margin-bottom: 0.5rem;
    border-radius: 4px;
    background: #f0f0f0;
}

.product h3 { margin: 0.5rem 0; font-size: 1rem; }
.product p { margin: 0.2rem 0; font-size: 0.9rem; }
.stars { color: gold; font-size: 1rem; }

@media (max-width: 768px) {
    .hero h1 {
        font-size: 2rem;
    }
    .hero p {
        font-size: 1rem;
    }
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="index.php" style="font-family: Georgia;">
            <img src="assets/logo.png" class="border rounded-circle" alt="" height="40">
            MAM Shop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link active border-bottom border-3 border-dark fw-bold" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="allproduct.php">All Products</a></li>
            </ul>

            <form class="d-flex">
                <a href="cart.php" class="btn btn-outline-dark">
                    <i class="bi-cart-fill me-1"></i>
                    Cart
                    <span class="badge bg-dark text-white ms-1 rounded-pill">
                        <?= array_sum($_SESSION['cart'] ?? []) ?>
                    </span>
                </a>
                <a href="login.php" class="btn btn-outline-dark ms-3">
                    <i class="bi bi-person-circle"></i>
                    Log In
                </a>
            </form>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1 style="font-family:Comfortaa; font-size:4em;">MERCHANT AND MECHANICS!</h1>
        <p style="font-size: 22px;">Discover the latest in tech innovation with exclusive discounts up to 50% off. Don’t miss limited-time offers!</p>
    </div>
</section>

<h2 class="section-title">Top 5 Recommended Products</h2>
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
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center;">No products available.</p>
<?php endif; ?>
</div>

<footer class="py-5 bg-dark">
  <div class="container text-center text-light">
    <p class="m-0">&copy; MAM Shop Website 2025</p>
    <p class="m-0">Creator: Lebron Setosta & Cyrus Songodanan</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
