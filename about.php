<?php
session_start();
include("db_connect.php");

$user_id = $_SESSION['user_id'] ?? null;
$fullname = "Guest";  // Default for guests
$cart_count = 0;      // Default cart count for guests

if ($user_id) {
  // Get user name
  $stmt = $DBConnect->prepare("SELECT fullname FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($fullname);
  $stmt->fetch();
  $stmt->close();

  // Get cart count
  $stmt = $DBConnect->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($cart_count);
  $stmt->fetch();
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us - MAM Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f9fbfd; }
    .hero {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 40vh;
      background: url('assets/hero.jpg') no-repeat center/cover;
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
    }
    main {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    .welcome_greeting {
      font-family: Comfortaa;
      font-size: 4em;
    }
    .topic-box {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.08);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    .topic-box h2 {
      font-weight: bold;
      margin-bottom: 1rem;
    }
    .founder-img {
      height: 300px;
      width: 100%;
      object-fit: cover;
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
        <li class="nav-item"><a class="nav-link" href="welcome.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active border-bottom border-3 border-dark fw-bold" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="allproduct.php">All Products</a></li>
      </ul>
      <form class="d-flex">
        <a href="cart.php" class="btn btn-outline-dark">
          <i class="bi-cart-fill me-1"></i> Cart
          <span class="badge bg-dark text-white ms-1 rounded-pill"><?= $cart_count ?></span>
        </a>
        <?php if ($user_id): ?>
          <a href="logout.php" class="btn btn-outline-danger ms-3">
            <i class="bi bi-box-arrow-right"></i> Log Out
          </a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-dark ms-3">
            <i class="bi bi-person-circle"></i> Log In
          </a>
        <?php endif; ?>
      </form>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="hero-content">
    <h1 class="welcome_greeting">Hi, <?= htmlspecialchars($fullname) ?>!</h1>
    <p style="font-size: 22px;">Learn more about MAM Shop, our mission and who we are.</p>
  </div>
</section>

<main>
  <div class="topic-box w-75 mx-auto">
    <h2>Who We Are</h2>
    <p>MAM Shop is your trusted online store for gadgets and accessories. Founded by Lebron Setosta and Cyrus Songodanan in 2025, we bring the best tech directly to you.</p>
  </div>
  <div class="topic-box w-75 mx-auto">
    <h2>Our Mission</h2>
    <p>We aim to make tech simple, accessible, and affordable for everyone. Great service, quick delivery, and top-rated products — that’s our promise.</p>
  </div>
  <div class="topic-box w-75 mx-auto">
    <h2>Why Choose Us?</h2>
    <ul>
      <li>✅ Best-selling gadgets</li>
      <li>✅ Exclusive promotions</li>
      <li>✅ Hassle-free shopping</li>
      <li>✅ Friendly support team</li>
    </ul>
  </div>

  <div class="topic-box w-75 mx-auto text-center">
    <h2>Meet the Founders</h2>
    <p><strong>Lebron Setosta</strong> & <strong>Cyrus Songodanan</strong> are passionate about technology and committed to delivering quality gadgets to your doorstep.</p>
    <div class="row mt-4 justify-content-center">
      <div class="col-md-5 mb-3">
        <img src="assets/leb.jpg" alt="Lebron Setosta" class="img-fluid rounded shadow founder-img">
        <h5 class="mt-2">Lebron Setosta</h5>
      </div>
      <div class="col-md-5 mb-3">
        <img src="assets/cyrus.jpg" alt="Cyrus Songodanan" class="img-fluid rounded shadow founder-img">
        <h5 class="mt-2">Cyrus Songodanan</h5>
      </div>
    </div>
  </div>
</main>

<footer class="py-5 bg-dark">
  <div class="container text-center text-light">
    <p class="m-0">&copy; MAM Shop 2025</p>
    <p class="m-0">Creators: Lebron Setosta & Cyrus Songodanan</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
