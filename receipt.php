<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $street = $_POST['street'];
    $province = $_POST['province'];
    $country = $_POST['country'];
    $zipcode = $_POST['zipcode'];
    $quantities = $_POST['quantities'];

    $stmt = $DBConnect->prepare("
        UPDATE users SET street=?, province=?, country=?, zipcode=?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssi", $street, $province, $country, $zipcode, $user_id);
    $stmt->execute();
    $stmt->close();

    $DBConnect->query("INSERT INTO transactions (user_id) VALUES ($user_id)");
    $transaction_id = $DBConnect->insert_id;

    foreach ($quantities as $product_id => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            $insert_item = $DBConnect->prepare("
                INSERT INTO transaction_items (transaction_id, product_id, quantity)
                VALUES (?, ?, ?)
            ");
            $insert_item->bind_param("iii", $transaction_id, $product_id, $quantity);
            $insert_item->execute();
            $insert_item->close();
        }
    }

    $DBConnect->query("DELETE FROM cart WHERE user_id = $user_id");
}

$stmt = $DBConnect->prepare("
    SELECT fullname, dob, phone, gmail, address, country, province, street, zipcode, username
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $DBConnect->prepare("
    SELECT id, created_at
    FROM transactions
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($transaction_id, $created_at);
$stmt->fetch();
$stmt->close();

$stmt = $DBConnect->prepare("
    SELECT p.name, p.price, ti.quantity
    FROM transaction_items ti
    JOIN products p ON ti.product_id = p.id
    WHERE ti.transaction_id = ?
");
$stmt->bind_param("i", $transaction_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Receipt - MAM Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="index.php">
      <img src="assets/Logo.png" class="border rounded-circle" height="40" alt="">
      MAM Shop
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link" href="welcome.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="allproduct.php">All Products</a></li>
      </ul>
      <a href="logout.php" class="btn btn-outline-danger ms-3"><i class="bi bi-person-circle"></i> Log Out</a>
    </div>
  </div>
</nav>

<div class="container my-5">
  <div class="card p-4">
    <h3 class="mb-3 text-center">Order Receipt</h3>

    <h5>Customer Info</h5>
    <ul class="list-group mb-4">
      <li class="list-group-item"><strong>Full Name:</strong> <?= htmlspecialchars($user['fullname']) ?></li>
      <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['gmail']) ?></li>
      <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></li>
      <li class="list-group-item"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></li>
      <li class="list-group-item"><strong>Address:</strong> <?= htmlspecialchars($user['street'] . ', ' . $user['province'] . ', ' . $user['country'] . ' - ' . $user['zipcode']) ?></li>
      <li class="list-group-item"><strong>Date:</strong> <?= date('F d, Y h:i A', strtotime($created_at)) ?></li>
    </ul>

    <h5>Order Summary</h5>
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Product</th>
          <th>Price (₱)</th>
          <th>Quantity</th>
          <th>Subtotal (₱)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): 
          $subtotal = $item['price'] * $item['quantity'];
          $total += $subtotal;
        ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($subtotal, 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">Total:</th>
          <th>₱<?= number_format($total, 2) ?></th>
        </tr>
      </tfoot>
    </table>

    <div class="text-center mt-4">
      <a href="welcome.php" class="btn btn-outline-dark">Back to Home</a>
    </div>
  </div>
</div>

<footer class="py-5 bg-dark text-light text-center">
  <div class="container">
    <p class="m-0">&copy; MAM Shop Website 2025</p>
    <p class="m-0">Creator: Lebron Setosta & Cyrus Songodanan</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
