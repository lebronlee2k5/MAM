<?php
$errors = [];
$old    = [];

include("db_connect.php");
$mysqli = $DBConnect;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    function postVal($k) { return htmlspecialchars($_POST[$k] ?? ''); }

    $name       = $old['name']       = postVal('name');
    $gender     = $old['gender']     = postVal('gender');
    $dob        = $old['dob']        = postVal('dob');
    $phone      = $old['phone']      = postVal('phone');
    $email      = $old['email']      = postVal('email');
    $street     = $old['street']     = postVal('street');
    $city       = $old['city']       = postVal('city');
    $state      = $old['state']      = postVal('state');
    $zip        = $old['zip']        = postVal('zip');
    $country    = $old['country']    = postVal('country');
    $username   = $old['username']   = postVal('username');
    $password   = $_POST['password'] ?? '';
    $confirmPw  = $_POST['confirmPassword'] ?? '';

    if (!preg_match('/^[a-zA-Z\s]{2,50}$/',$name))
        $errors['name'] = 'Full name: 2‑50 letters/spaces.';

    if (!in_array($gender,['male','female','other']))
        $errors['gender'] = 'Please select gender.';

    $dob_time = strtotime($dob);
    if (!$dob_time || time()-$dob_time < 18*365.25*24*60*60)
        $errors['dob'] = 'Must be at least 18 years old.';

    if (!preg_match('/^09\d{9}$/',$phone))
        $errors['phone'] = "Phone must start with 09 and be 11 digits.";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Invalid email.';

    if (!preg_match('/^[a-zA-Z0-9\s\.,#\-]{5,100}$/',$street))
        $errors['street'] = 'Street 5‑100 valid chars.';

    if (!preg_match('/^[a-zA-Z\s]{2,50}$/',$city))
        $errors['city'] = 'City 2‑50 letters/spaces.';

    if (!preg_match('/^[a-zA-Z\s]{2,50}$/',$state))
        $errors['state'] = 'Province/State 2‑50 letters/spaces.';

    if (!preg_match('/^\d{4}$/',$zip))
        $errors['zip'] = 'ZIP must be 4 digits.';

    if (!preg_match('/^[a-zA-Z\s]+$/',$country))
        $errors['country'] = 'Country letters/spaces only.';

    if (!preg_match('/^\w{5,20}$/',$username))
        $errors['username'] = 'Username 5‑20 letters/numbers/_ .';

    if (strlen($password) < 8 ||
        !preg_match('/[A-Z]/',$password) ||
        !preg_match('/[a-z]/',$password) ||
        !preg_match('/\d/',$password)  ||
        !preg_match('/[\W_]/',$password))
        $errors['password'] = 'Password ≥ 8, upper, lower, digit, special.';

    if ($password !== $confirmPw)
        $errors['confirmPassword'] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = $mysqli->prepare(
            "SELECT gmail, username FROM users WHERE gmail = ? OR username = ? LIMIT 1"
        );
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $stmt->bind_result($dupMail, $dupUser);
        if ($stmt->fetch()) {
            if ($dupMail == $email) $errors['email'] = 'E‑mail already registered.';
            if ($dupUser == $username) $errors['username'] = 'Username taken.';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $address = "$street, $city";
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("
            INSERT INTO users
              (fullname, dob, phone, gmail, address, country, province, street, zipcode, username, password)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param('sssssssssss',
            $name, $dob, $phone, $email, $address, $country, $state, $street, $zip, $username, $hash);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Redirecting to login…'); window.location.href='login.php';</script>";
            exit;
        } else {
            $errors['general'] = 'DB error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>MAM Shop - Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{background:#f9fdfd;}
.register-card{border-radius:1rem;box-shadow:0 4px 12px rgba(0,0,0,.1);}
h4.section-title{border-bottom:2px solid #000;padding-bottom:5px;margin-bottom:20px;font-weight:600;}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="index.php" style = "font-family:Georgia;">
        <img src="assets/Logo.png" class="border rounded-circle" alt="" height="40">    
        MAM Shop</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>
      <a href="login.php" class="btn btn-outline-dark ms-3 active"><i class="bi bi-person-circle"></i> Log In</a>
    </div>
  </div>
</nav>

<div class="container d-flex justify-content-center align-items-center my-5">
  <div class="col-12 col-md-10 col-lg-8 col-xl-7">
    <div class="card register-card">
      <div class="card-body p-5">
        <h3 class="text-center mb-4">Create Account</h3>

        <?php if(isset($errors['general'])):?>
          <div class="alert alert-danger"><?= $errors['general']; ?></div>
        <?php endif;?>

        <form method="post" novalidate>
          <h4 class="section-title">Personal Information</h4>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" name="name" id="fullName"
                       value="<?= $old['name']??'' ?>" required>
                <label for="fullName">Full Name</label>
              </div>
              <?php if(isset($errors['name'])):?><div class="text-danger"><?= $errors['name'];?></div><?php endif;?>
            </div>
            <div class="col-md-6">
              <label class="form-label d-block text-center">Gender</label>
              <?php foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $v=>$lbl):?>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" value="<?= $v?>"
                    <?= ($old['gender']??'')==$v?'checked':'' ?> required>
                  <label class="form-check-label"><?= $lbl?></label>
                </div>
              <?php endforeach;?>
              <?php if(isset($errors['gender'])):?><div class="text-danger"><?= $errors['gender'];?></div><?php endif;?>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" name="dob" id="dob"
                       value="<?= $old['dob']??'' ?>" required>
                <label for="dob">Date of Birth</label>
              </div>
              <?php if(isset($errors['dob'])):?><div class="text-danger"><?= $errors['dob'];?></div><?php endif;?>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="tel" class="form-control" name="phone" id="phone"
                       value="<?= $old['phone']??'' ?>" required>
                <label for="phone">Phone Number</label>
              </div>
              <?php if(isset($errors['phone'])):?><div class="text-danger"><?= $errors['phone'];?></div><?php endif;?>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="email" class="form-control" name="email" id="email"
                       value="<?= $old['email']??'' ?>" required>
                <label for="email">Email</label>
              </div>
              <?php if(isset($errors['email'])):?><div class="text-danger"><?= $errors['email'];?></div><?php endif;?>
            </div>
          </div>

          <h4 class="section-title">Address Details</h4>
          <div class="row g-3 mb-4">
            <?php
              $fields=['street'=>'Street','city'=>'City','state'=>'Province / State',
                       'zip'=>'Zip Code','country'=>'Country'];
              foreach($fields as $f=>$lbl):
            ?>
              <div class="<?= $f==='street'?'col-12':'col-md-6';?>">
                <div class="form-floating">
                  <input type="text" class="form-control" name="<?= $f?>" id="<?= $f?>"
                         value="<?= $old[$f]??''?>" required>
                  <label for="<?= $f?>"><?= $lbl?></label>
                </div>
                <?php if(isset($errors[$f])):?><div class="text-danger"><?= $errors[$f];?></div><?php endif;?>
              </div>
            <?php endforeach;?>
          </div>

          <h4 class="section-title">Account Details</h4>
          <div class="row g-3 mb-4">
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control" name="username" id="username"
                       value="<?= $old['username']??'' ?>" required>
                <label for="username">Username</label>
              </div>
              <?php if(isset($errors['username'])):?><div class="text-danger"><?= $errors['username'];?></div><?php endif;?>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="password" class="form-control" name="password" id="password" required>
                <label for="password">Password</label>
              </div>
              <?php if(isset($errors['password'])):?><div class="text-danger"><?= $errors['password'];?></div><?php endif;?>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="password" class="form-control" name="confirmPassword"
                       id="confirmPassword" required>
                <label for="confirmPassword">Confirm Password</label>
              </div>
              <?php if(isset($errors['confirmPassword'])):?><div class="text-danger"><?= $errors['confirmPassword'];?></div><?php endif;?>
            </div>
          </div>

          <div class="d-flex gap-3">
            <button type="reset" class="btn btn-outline-dark flex-fill">Clear</button>
            <button type="submit" class="btn btn-outline-success flex-fill">Register</button>
          </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
