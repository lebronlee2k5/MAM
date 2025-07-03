<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $DBConnect->prepare("SELECT street, province, country, zipcode FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($street, $province, $country, $zipcode);
$stmt->fetch();
$stmt->close();

$stmt = $DBConnect->prepare("SELECT c.product_id, p.name, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirm Your Order</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f8fdfc; }
.container { max-width: 800px; margin-top: 30px; }
table th, table td { vertical-align: middle; }
</style>
</head>
<body>
<div class="container bg-white p-4 rounded shadow">
  <h2 class="text-center mb-4">Confirm Your Order</h2>
  <form method="post" action="receipt.php">
    <div class="row g-2 mb-4">
      <div class="col-md-6">
        <input type="text" class="form-control" name="street" placeholder="Street Address" value="<?= htmlspecialchars($street) ?>" required>
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" name="province" placeholder="Province" value="<?= htmlspecialchars($province) ?>" required>
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" name="country" placeholder="Country" value="<?= htmlspecialchars($country) ?>" required>
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" name="zipcode" placeholder="Zip Code" value="<?= htmlspecialchars($zipcode) ?>" required>
      </div>
    </div>

    <h5 class="mb-3">Review Items</h5>
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Product</th>
            <th>Price (₱)</th>
            <th>Quantity</th>
            <th>Subtotal (₱)</th>
          </tr>
        </thead>
        <tbody>
          <?php $total = 0; foreach ($cart_items as $index => $item): ?>
            <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= number_format($item['price'], 2) ?></td>
              <td>
                <input type="number" name="quantities[<?= $item['product_id'] ?>]" class="form-control quantity-input" value="<?= $item['quantity'] ?>" min="1">
              </td>
              <td class="subtotal" data-price="<?= $item['price'] ?>">
                <?= number_format($subtotal, 2) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-end">Total:</th>
            <th id="totalAmount">₱<?= number_format($total, 2) ?></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex gap-2">
      <a href="cart.php" class="btn btn-outline-danger flex-fill">Cancel</a>
      <button type="submit" class="btn btn-success flex-fill">Confirm & Proceed to Receipt</button>
    </div>
  </form>
</div>

<script>
document.querySelectorAll('.quantity-input').forEach(input => {
  input.addEventListener('input', updateTotals);
});

function updateTotals() {
  let total = 0;
  document.querySelectorAll('tbody tr').forEach(row => {
    const price = parseFloat(row.querySelector('.subtotal').getAttribute('data-price'));
    const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
    const subtotal = price * quantity;
    row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
    total += subtotal;
  });
  document.getElementById('totalAmount').textContent = '₱' + total.toFixed(2);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
