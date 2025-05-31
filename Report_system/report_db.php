<?php 
include_once '../login_system/server.php'; // Include the server connection file
// require_once '../for_hackker/5d2d3ceb7abe552344276d47d36a8175b7aeb250a9bf0bf00e850cd23ecf2e43.php'; // Include the database connection file
// include_once '../Register_system/login.php'; // Include the session file
session_start();
// if (!isset($_SESSION['student_id'])) {
//     die("ยังไม่ได้ login หรือ session หมดอายุ");
//     header("Location: ../Register_system/login.php"); // Redirect to login page}
// }
$today = date("md"); // MMDD
$sql = "SELECT COUNT(*) AS count FROM report WHERE DATE(report_date) = CURDATE()";// นับจำนวนรายงานที่ส่งเข้ามาในวันนั้น
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$count = $row['count'] + 1;
$nn = str_pad($count, 2, "0", STR_PAD_LEFT);
$case_id = $today . $nn;

$place = $_POST['place'];
$floor = $_POST['floor'];
$room = $_POST['room'];
$problem_type = $_POST['problem_type'];
$description = $_POST['description'];
 
// $std_id = $_SESSION['student_id'];
if (!ctype_digit($floor) || strlen($floor) !== 1) {
        // $_SESSION['error'] = 'ชั้นต้องเป็นตัวเลข 1 หลัก';
        echo "<script>alert('ชั้นต้องเป็นตัวเลข 1 หลัก'); window.location.href = '../Report_system/report.php';</script>";
        exit();
}
if (!empty($room)){
    if (!ctype_digit($room) || strlen($room) !== 4) {
        echo "<script>alert('เลขห้องต้องมี 4 ตัว'); window.location.href = '../Report_system/report.php';</script>";
        exit();
    }
}

$dir = "Report_pic/";
$year_month = date("Y-m");
$targetDir = $dir . $year_month . '/'. $case_id . '/'; // โฟลเดอร์ที่เก็บไฟล์
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
}
$maxFileSize = 15 * 1024 * 1024;//15 MB
$allowedExtensions = ['jpg', 'jpeg', 'png', 'heic', 'gif'];
$notallowedExtensions = ['php', 'html', 'js', 'css', 'exe', 'bat', 'sh']; // นามสกุลที่ไม่อนุญาต
$uploadedFiles = [];

if (!empty($_FILES['file']['name'][0])) {
    $fileCount = count($_FILES['file']['name']);
    
    // ลิมิต 4 ไฟล์
    $maxFiles = 4;
    $fileCount = min($fileCount, $maxFiles);

    for ($i = 0; $i < $fileCount; $i++) {
        $fileExt = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION); // ดึงนามสกุลไฟล์
        
        if (!in_array($fileExt, $allowedExtensions)) {
            echo "<script>alert('จะทำอะไรเอ่ย'); window.location.href = '../Report_system/report.php';</script>";
            exit();
        }
        

        $newFileName = $case_id . '-' . ($i + 1) . '.' . $fileExt; // ตั้งชื่อใหม่แบบ case_id-ลำดับ
        $img = $targetDir . $newFileName;

        $tmpName = $_FILES['file']['tmp_name'][$i];
        $fileSize = $_FILES['file']['size'][$i];

        if ($fileSize > $maxFileSize) {
            echo "<script>alert('ไฟล์ $newFileName มีขนาดเกิน 15 MB และไม่สามารถอัปโหลดได้');</script>";
            continue;
        }

        if (move_uploaded_file($tmpName, $img)) {
            echo "ไฟล์ <b>$newFileName</b> อัปโหลดเสร็จสิ้น<br>";
            $uploadedFiles[] = $newFileName; // เก็บชื่อไฟล์อย่างเดียว
        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ <b>$newFileName</b><br>";
        }
    }
} else {
    // echo "<script>alert('⚠️ ไม่ได้เลือกไฟล์ใด ๆ สำหรับอัปโหลด');  window.location.href = '../Report_system/report.php';</script>";
    // header ("Location: ../Report_system/report.php");
}

$query = "INSERT INTO report (case_id, student_id,place, floor, room, problem_type, description, img) VALUES ('$case_id','$std_id','$place', '$floor', '$room', '$problem_type', '$description', '$img')";  
if (mysqli_query($connect, $query) === TRUE) {
    echo "New record created successfully";
    echo "<script>alert('ส่งรายงานเรียบร้อยแล้ว');  window.location.href = '../Report_system/report.php';</script>";
} else {
    // echo "Error: " . $query . "<br>" . mysqli_error($connect);
    echo  mysqli_error($connect);
}
?>