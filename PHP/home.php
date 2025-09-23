<?php
// session_start();
// if (!isset($_SESSION['username'])) {
//     $_SESSION['msg'] = "Please login first.";
//     header("Location: ../login_system/login.php");
// }
include_once '../login_system/server.php';
$sql = "SELECT status, COUNT(*) AS total FROM report GROUP BY status";
$result = $connect->query($sql);

$pending = 0;
$inprogress = 0;
$done = 0;

// วนลูปเก็บค่า
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['status'] === 'Pending') {
            $pending = $row['total'];
        } elseif ($row['status'] === 'Inprogress') {
            $inprogress = $row['total'];
        } elseif ($row['status'] === 'Done') {
            $done = $row['total'];
        }
    }
}

$connect->close();
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
    <link rel="website icon" type="png" href="https://inf.bodin.ac.th/_resx/upload/img/brand/logo/color.png">

<body>
    <nav class="navbar">
        <div class="logo-info">
            <img class="logo" src="../cropped-bodin.png" alt="logo">
            <p class="school_name">Bodindecha (Sing Singhaseni) school</p>
        </div>
        <ul class="container">
            <li class="home">
                <a href="home.php">Home</a>
            </li>
            <li class="report">
                <a href="../Report_system/report_lab.php">Report</a>
            </li>
            <li class="contact">
                <a href="../HTML/contact.html">Contact</a>
            </li>
        </ul>
        <div class="icon-container">
            <a href="home.html"><i class="fa-solid fa-house home" style="color: #ffffff;"></i></a>
            <a href="report.html"><i class="fa-solid fa-plus" style="color: #ffffff;"></i></a>
            <a href="contact.html"><i class="fa-solid fa-phone" style="color: #ffffff;"></i></a>
        </div>
    </nav>
    <div class="list-container">
        <div class="list-item">
            <span>รอดำเนินการ</span>
            <span class="number"><?php echo $pending; ?></span>
        </div>
        <div class="line"></div>
        <div class="list-item">
            <span>กำลังดำเนินการ</span>
            <span class="number"><?php echo $inprogress; ?></span>
        </div>
        <div class="line"></div>
        <div class="list-item">
            <span>ดำเนินการเสร็จสิ้น</span>
            <span class="number"><?php echo $done; ?></span>
        </div>
    </div>
</body>

</html>