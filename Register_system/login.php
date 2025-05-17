<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../CSS/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="website icon" type="png" href="../img/Logo bodin.PNG">
</head>
<body>
    <nav class="navbar">
        <div class="logo-info">
           <img class="logo" src="../cropped-bodin.png" alt="logo">
           <p class="school_name">Bodindecha (Sing Singhaseni) school</p>
           <p class = "center">Login</p>
        </div>
        <!-- <div class="head">
            <p>Login</p>
        </div> -->
    </nav>
    <form class = "login-part" action = "login_process.php" method="post">
        <!-- <?php if (isset($_SESSION['error'])): ?>
            <div class="error-msg"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?> -->
        <div class = "element">
            <div class = "m-b"></div>
            <b><div class = "welcome">Welcome !</div></b>
            <div class = "line"></div>
            <div class = "fill">
                <div class = "student-id">
                    <label for="std_id">Student Id</label>
                    <input type="number" name = "std_id" placeholder="Don't add @bodin.ac.th" required>
                </div>
                <div class = "password">
                    <label for="pass">Password</label>
                    <input type="password" id="password" name="password" required>
                    <i id = "eyepassword" class="fa-solid fa-eye password-icon" style="color: #3a5f9c;"></i>
                </div>
                <div class ="but-log">
                    <!-- <a href="../PHP/home.php"><button type = "submit" name="login_user">Login <i class="fa-solid fa-caret-right fa-sm" style="color: #ffffff;"></i></button></a> -->
                    <button type = "submit" name="login_user">Login <i class="fa-solid fa-caret-right fa-sm" style="color: #ffffff;"></i></button>
                </div>
            </div>
        </div>
    </form>
    <script src="../JS/login.js"></script>
</body>
</html>