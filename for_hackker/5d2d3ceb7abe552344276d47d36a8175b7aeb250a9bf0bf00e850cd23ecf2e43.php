<?php
$base_dir = realpath("safe"); // โฟลเดอร์ปลอดภัย
$filename = $_REQUEST['file']; // จะรับทั้ง GET หรือ POST ก็ได้
$path = realpath($base_dir . "/" . $filename);

// ตรวจสอบว่าไฟล์อยู่ในโฟลเดอร์ปลอดภัย และมีจริง
if ($path && strpos($path, $base_dir) === 0 && file_exists($path)) {
    readfile($path);
} else {
    readfile("../safe/hello.txt");
}
?>