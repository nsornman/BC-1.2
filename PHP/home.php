<?php
    // session_start();
    // if (!isset($_SESSION['username'])) {
    //     $_SESSION['msg'] = "Please login first.";
    //     header("Location: ../login_system/login.php");
    // }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../CSS/home.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anuphan">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="website icon" type="png" href="img/Logo bodin.PNG">
<body>
    <nav class="navbar">
        <div class="logo-info">
            <img class="logo" src="../cropped-bodin.png" alt="logo">
            <p class="school_name">Bodindecha (Sing Singhaseni) school</p>
        </div>
        <ul class="container">
            <li class = "home">
                <a href="home.php">Home</a>
            </li>
            <li class = "report">
                <a href="../Report_system/report.php">Report</a>
            </li>
            <li class="contact">
                <a href="../HTML/contact.html">Contact</a>
            </li>
        </ul>
        <div class = "icon-container">
            <a href="home.html"><i class="fa-solid fa-house home" style="color: #ffffff;"></i></a>
            <a href="report.html"><i class="fa-solid fa-plus" style="color: #ffffff;"></i></a>
            <a href="contact.html"><i class="fa-solid fa-phone" style="color: #ffffff;"></i></a>
        </div>
    </nav>
    <div class="list-container">
        <div class="list-item">
          <span>รอดำเนินการ</span>
          <span class="number">5</span>
        </div>
        <div class="line"></div>
        <div class="list-item">
          <span>กำลังดำเนินการ</span>
          <span class="number">5</span>
        </div>
        <div class = "line"></div>
        <div class="list-item">
          <span>ดำเนินการเสร็จสิ้น</span>
          <span class="number">5</span>
        </div>
    </div>
</body>
</html>