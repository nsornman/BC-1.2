<?php 
include_once '../login_system/server.php'; // Include the server connection file
// require_once '../for_hackker/5d2d3ceb7abe552344276d47d36a8175b7aeb250a9bf0bf00e850cd23ecf2e43.php'; // Include the database connection file
// include_once '../Register_system/login.php'; // Include the session file
session_start();
// if (!isset($_SESSION['student_id'])) {
//     die("ยังไม่ได้ login หรือ session หมดอายุ");
//     header("Location: ../Register_system/login.php"); // Redirect to login page}
// }
$year = date("y");
$month = (int)date("m");
$day = (int)date("d"); 
$today = $year . $month . $day;
$sql = "SELECT COUNT(*) AS count FROM report WHERE DATE(report_date) = CURDATE()";// count report
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$count = $row['count'] + 1;
$nn = str_pad($count, 2, "0", STR_PAD_LEFT);
$case_id = $today. $nn;

$place = mysqli_real_escape_string($connect, $_POST['place']);
$floor = mysqli_real_escape_string($connect, $_POST['floor']);
$room = mysqli_real_escape_string($connect, $_POST['room']);
$problem_type = mysqli_real_escape_string($connect, $_POST['problem_type']);
$description = mysqli_real_escape_string($connect, $_POST['description']);
$explane = mysqli_real_escape_string($connect, $_POST['explane']);
 
// $std_id = $_SESSION['student_id'];
if (!is_numeric($floor) || strlen($floor) !== 1) {       
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
$targetDir = $dir . $year_month . '/'. $case_id . '/'; // folder path to save images
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true); // create folder
}
$maxFileSize = 15 * 1024 * 1024;//15 MB
$allowedExtensions = ['jpg', 'jpeg', 'png', 'heic', 'gif'];
$uploadedFiles = [];

if (!empty($_FILES['file']['name'][0])) {
    $fileCount = count($_FILES['file']['name']);
    $maxFiles = 4;
    $fileCount = min($fileCount, $maxFiles);

    for ($i = 0; $i < $fileCount; $i++) {
        $fileExt = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);

        if (!in_array(strtolower($fileExt), $allowedExtensions)) {
            echo "<script>alert('ประเภทไฟล์ไม่ถูกต้อง'); window.location.href = '../Report_system/report.php';</script>";
            exit();
        }

        $newFileName = $case_id . '-' . ($i + 1) . '.' . $fileExt;
        $fullPath = $targetDir . $newFileName;
        $tmpName = $_FILES['file']['tmp_name'][$i];
        $fileSize = $_FILES['file']['size'][$i];

        if ($fileSize > $maxFileSize) {
            echo "<script>alert('ไฟล์ $newFileName มีขนาดเกิน 15 MB');</script>";
            continue;
        }

        if (move_uploaded_file($tmpName, $fullPath)) {
            $uploadedFiles[] = $newFileName;
        }
        else {
            echo "ไม่สามารถอัปโหลดไฟล์ $newFileName ได้";
            echo "<script>alert('ไม่สามารถอัปโหลดไฟล์ $newFileName ได้');</script>";
            // continue;
        }
    }

// upload images to database
    
} 
if (!empty($uploadedFiles)) {
        $relativePaths = array_map(function($path) {
        return substr($path, strpos($path, 'Report_pic/'));
        }, $uploadedFiles);
        $imgString = implode(',', $relativePaths);
        $escaped_img = mysqli_real_escape_string($connect, $imgString);
    } else {
        $escaped_img = '';
    }
$query = "INSERT INTO report (case_id,  place, floor, room, explane, problem_type, description, img)  VALUES ('$case_id', '$place', '$floor', '$room', '$explane', '$problem_type', '$description', '$escaped_img')";
if (mysqli_query($connect, $query) === TRUE) {

    echo "New record created successfully";
    echo "<script>alert('ส่งรายงานเรียบร้อยแล้ว');  window.location.href = '../Report_system/report_lab.php';</script>";
} else {
    // echo "Error: " . $query . "<br>" . mysqli_error($connect);
    echo  mysqli_error($connect);
    echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($connect) . "'); window.location.href = '../Report_system/report_lab.php';</script>";
    exit();
}
// if (!empty($uploadedFiles)) {
//         $imgString = implode(',', $uploadedFiles);
//         $escaped_img = mysqli_real_escape_string($connect, $imgString);

//         if (mysqli_query($connect, $query)) {
//             echo "<script>alert('ส่งรายงานเรียบร้อยแล้ว');  window.location.href = '../Report_system/report_lab.php';</script>";
//         } else {
//             echo "เกิดข้อผิดพลาด: " . mysqli_error($connect);
//         }
//     }
// if (empty($uploadedFiles)) {
//     if (mysqli_query($connect, $query)) {
//             echo "<script>alert('ส่งรายงานเรียบร้อยแล้ว');  window.location.href = '../Report_system/report_lab.php';</script>";
//         } else {
//             echo "เกิดข้อผิดพลาด: " . mysqli_error($connect);
//         }
// }
// else {
//         echo "<script>alert('ไม่มีไฟล์ถูกอัปโหลด'); window.location.href = '../Report_system/report_lab.php';</script>";
// }
?>