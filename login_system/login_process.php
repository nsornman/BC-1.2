<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['std_id'] ?? '';
    $password = $_POST['password'] ?? '';
    $zone = $_POST['zone'] ?? '';

    if (empty($studentId) || empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        echo "<script>alert('กรุณาใส่ username password'); window.location.href = '../login_system/login.php'</script>";
        exit();
    }
    // if (!is_numeric($studentId) || strlen($studentId) !== 5) {
    //     $_SESSION['error'] = 'รหัสนักเรียนต้องเป็นตัวเลขไม่เกิน 5 หลัก';
    //     echo "<script>alert('รหัสนักเรียนต้องเป็นตัวเลข 5 หลัก'); window.location.href = '../login_system/login.php'</script>";
    //     exit();
    // }


    $postData = http_build_query([
        'username' => $studentId,
        'password' => $password,
        'zone' => $zone
    ]);


    $ch = curl_init('https://sapi.bodin.ac.th/v1/authen.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    if ($response === false) {
        echo "cURL Error: " . curl_error($ch);
        curl_close($ch);
        exit();
    }

    curl_close($ch);

    echo "<pre>Raw API Response:\n";
    print_r($response);
    echo "</pre>";


    

    print_r($result);
    // แปลงค่าที่ได้จาก API
    $result = json_decode($response, true);
    if ($result === null) {
            echo "JSON decode error: " . json_last_error_msg();
            exit();
        }
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
