<?php
session_start();

require_once '../config/config.php';
require_once '../models/AdminUserModel.php';

// Create an instance of AdminUserModel
$userModel = new AdminUserModel($pdo);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Perform validation and check credentials using AdminUserModel
    $user = $userModel->authenticateUser($email, $password);

    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];         // Store user ID
        $_SESSION['email'] = $user['email'];             // Store email
        $_SESSION['last_name'] = $user['last_name'];       // Store username
        $_SESSION['first_name'] = $user['first_name'];     // Store full name
        $_SESSION['role'] = $user['role'];               // Store user role

        header('Location: dashboard/dashboard.php'); // Redirect to admin dashboard
        exit();
    } else {
        $error = 'Thông tin đăng nhập không chính xác'; // Set error message for invalid credentials
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        /* ---------- SIGN IN ---------- */
.sign-in__wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100vw;
    height: 100vh;
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;
  }
  
  .sign-in__backdrop {
    position: absolute;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.3);
  }
  
  .sign-in__wrapper form {
    width: 24rem;
    max-width: 90%;
    z-index: 1;
    animation: showSignInForm 1s;
  }
  
  .sign-in__wrapper form img {
    width: 4rem;
  }
  
  @keyframes showSignInForm {
    0%,
    30% {
      transform: translate(0, -150%);
    }
    70%,
    90% {
      transform: translate(0, 1rem);
    }
    80%,
    100% {
      transform: translate(0, 0);
    }
  }
  
    </style>
</head>

<body class="my-login-page">
    <div class="sign-in__wrapper" style="background-image: url('assets/images/background-of-gym-with-big-window-vector.jpg');">
        <div class="sign-in__backdrop"></div>
        <form method="POST" class="shadow p-4 bg-white rounded">
            <img class="mx-auto d-block mb-2" src="assets/images/logo.png" alt="logo">
            <div class="h4 mb-2 text-center">Đăng nhập</div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mb-2"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="<?php if (isset($email)) echo htmlspecialchars($email); ?>" placeholder="Tên đăng nhập" required>
            </div>
            <div class="form-group mb-3">
                <label for="password">Mật khẩu</label>
                <input id="password" type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            <a class="btn btn-secondary w-100 mt-3" href="#">Trở về</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="js/my-login.js"></script>
</body>
</html>