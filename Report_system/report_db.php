<?php 
include_once '../Register_system/server.php'; // Include the server connection file
$place = $_POST['place'];
$floor = $_POST['floor'];
$room = $_POST['room'];
$problem_type = $_POST['problem_type'];
$description = $_POST['description'];
$today = date("md"); // MMDD เช่น "0516"

// นับจำนวนรายงานที่ส่งเข้ามาในวันนั้น
$sql = "SELECT COUNT(*) AS count FROM report WHERE DATE(report_date) = CURDATE()";
$result = $connect->query($sql);
$row = $result->fetch_assoc();

// เลขลำดับใหม่
$count = $row['count'] + 1;

// เติม 0 ข้างหน้าให้ NN เป็น 2 หลัก เช่น 01, 02, 10
$nn = str_pad($count, 2, "0", STR_PAD_LEFT);

// สร้าง case_id เช่น "051601"
$case_id = $today . $nn;
$query = "INSERT INTO report (case_id,place, floor, room, problem_type, description) VALUES ('$case_id','$place', '$floor', '$room', '$problem_type', '$description')";  
if (mysqli_query($connect, $query) === TRUE) {
    echo "New record created successfully";
} else {
    // echo "Error: " . $query . "<br>" . mysqli_error($connect);
    echo  mysqli_error($connect);
}
?>