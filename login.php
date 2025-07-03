<?php
session_start();

$error = '';
$redirect = $_GET['redirect'] ?? 'welcome.php';

include("db_connect.php");
$conn = $DBConnect;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "<div class='alert alert-danger mt-2'>Username and password required.</div>";
    } else {
        $stmt = $conn->prepare("
            SELECT id, fullname, gmail, username, password
            FROM users
            WHERE username = ?
            LIMIT 1
        ");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['fullname']  = $row['fullname'];
                $_SESSION['gmail']     = $row['gmail'];
                $_SESSION['username']  = $row['username'];

                header("Location: $redirect");
                exit;
            }
        }

        $error = "<div class='alert alert-danger mt-2'>Invalid username or password.</div>";
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>MAM Shop - Log In</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { background: #f9fdfd; }
    .login-card { border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,.1); }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="index.php" style="font-family:Georgia;">
      <img src="assets/Logo.png" class="border rounded-circle" height="40" alt=""> MAM Shop
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>
      <form class="d-flex">
        <a href="cart.php" class="btn btn-outline-dark">
          <i class="bi-cart-fill me-1"></i> Cart
          <span class="badge bg-dark text-white ms-1 rounded-pill"><?= array_sum($_SESSION['cart'] ?? []) ?></span>
        </a>
        <a href="login.php" class="btn btn-outline-dark active ms-3">
          <i class="bi bi-person-circle"></i> Log In
        </a>
      </form>
    </div>
  </div>
</nav>

<div class="container vh-100 d-flex justify-content-center align-items-center">
  <div class="col-12 col-md-8 col-lg-6 col-xl-5">
    <div class="card login-card">
      <div class="card-body p-5">
        <h3 class="text-center mb-4">Log In</h3>
        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?redirect=<?= urlencode($redirect) ?>">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            <label for="username">Username</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
          </div>
          <?= $error ?>
          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-outline-dark btn-lg">Log In</button>
          </div>
          <div class="text-center my-3">Don't have an account yet?  <a href="register.php">Register</a></div>
        </form>
      </div>
    </div>
  </div>
</div>

<footer class="py-5 bg-dark">
  <div class="container text-center text-light">
    <p class="m-0">&copy; MAM Shop Website 2025</p>
    <p class="m-0">Creator: Lebron Setosta & Cyrus Songodanan</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
