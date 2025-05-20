<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['std_id'] ?? '';
    $password = $_POST['password'] ?? '';

    // เตรียมข้อมูลแบบ form-data
    $postData = http_build_query([
        'std_id' => $studentId,
        'password' => $password
    ]);

    // ส่งไปยัง Server API ที่รับ form-data
    $ch = curl_init('https://your-api-server.com/login'); // 🔁 เปลี่ยนเป็น URL API ของคุณ
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    // แปลงค่าที่ได้จาก API
    $result = json_decode($response, true);

    // เช็คว่ามีจริงมั้ย
    if ($result && $result['status'] === 'success') {
        $_SESSION['student_id'] = $result['user']['std_id'];
        

        header('Location: ../home.php'); 
        exit();
    } else {
        // ล้มเหลว กลับไป login พร้อมข้อความ
        $_SESSION['error'] = $result['message'] ?? 'เข้าสู่ระบบไม่สำเร็จ';
        header('Location: ../Register_system/login.php');
        exit();
    }
}
?>