<?php
require_once '../for_hackker/5d2d3ceb7abe552344276d47d36a8175b7aeb250a9bf0bf00e850cd23ecf2e43.php';
session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentId = $_POST['std_id'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if(empty($studentId) || empty($password)) {
            $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            echo "<script>alert('กรุณาใส่ username password'); window.location.href = '../login_system/login.php'</script>";
            exit();
        }   
        if (!is_numeric($studentId) || strlen($studentId) !== 5) {
            $_SESSION['error'] = 'รหัสนักเรียนต้องเป็นตัวเลขไม่เกิน 5 หลัก';
            echo "<script>alert('รหัสนักเรียนต้องเป็นตัวเลข 5 หลัก'); window.location.href = '../login_system/login.php'</script>";
            exit();
        }

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
            echo "<script>alert('โปรดตรวจสอบเลขประจำตัวหรือรหัสว่าใส่ถูกต้องหรือไม่'); window.location.href = '../login_system/login.php'</script>";
            exit();
        }
    }

?>