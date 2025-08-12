<?php 

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');


include_once '../login_system/server.php'; 
require_once dirname(__DIR__) . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// NOTE: Login is not required for this form as per the original file's structure.
// session_start();
// if (!isset($_SESSION['student_id'])) { ... }


$year = date("y");
$month = (int)date("m");
$day = (int)date("d"); 
$today = $year . $month . $day;
$sql_count = "SELECT COUNT(*) AS count FROM report WHERE DATE(report_date) = CURDATE()";
$result_count = $connect->query($sql_count);
$row_count = $result_count->fetch_assoc();
$count = ($row_count['count'] ?? 0) + 1;
$nn = str_pad($count, 2, "0", STR_PAD_LEFT);
$case_id = $today. $nn;

$place = mysqli_real_escape_string($connect, $_POST['place'] ?? '');
$floor = mysqli_real_escape_string($connect, $_POST['floor'] ?? '');
$room = mysqli_real_escape_string($connect, $_POST['room'] ?? '');
$problem_type = mysqli_real_escape_string($connect, $_POST['problem_type'] ?? '');
$description = mysqli_real_escape_string($connect, $_POST['description'] ?? '');
$explane = mysqli_real_escape_string($connect, $_POST['explane'] ?? '');

if (empty($place) || empty($floor) || empty($problem_type)) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลที่จำเป็น: สถานที่, ชั้น, และประเภทปัญหา']);
    exit();
}


$escaped_img = '';
if (!empty($_FILES['file']['name'][0])) {
    $targetDir = 'Report_pic/' . date("Y-m") . '/' . $case_id . '/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $uploadedFiles = [];
    $maxFileSize = 15 * 1024 * 1024; 
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileCount = count($_FILES['file']['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = $_FILES['file']['name'][$i];
        $fileSize = $_FILES['file']['size'][$i];
        $fileTmpName = $_FILES['file']['tmp_name'][$i];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) continue;
        if ($fileSize > $maxFileSize) continue;

        $newFileName = $case_id . '-' . ($i + 1) . '.' . $fileExt;
        $fullPath = $targetDir . $newFileName;

        if (move_uploaded_file($fileTmpName, $fullPath)) {
            $uploadedFiles[] = $fullPath;
        }
    }
    if(!empty($uploadedFiles)){
        $escaped_img = mysqli_real_escape_string($connect, implode(',', $uploadedFiles));
    }
}

$query = "INSERT INTO report (case_id, place, floor, room, explane, problem_type, description, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, "ssssssss", $case_id, $place, $floor, $room, $explane, $problem_type, $description, $escaped_img);

if (mysqli_stmt_execute($stmt)) {
    // Email Notification Logic
    $recipient_email = '44358@bodin.ac.th';
    if (filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);
        $email_sent = false;
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nachakhun1998@gmail.com';
            $mail->Password   = 'crew pmao hgli efac';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom('noreply@reportsys.bodin.ac.th', 'ระบบแจ้งซ่อม ร.ร.บดินทรเดชา');
            $mail->addAddress($recipient_email);
            $mail->isHTML(true);
            $mail->Subject = 'ยืนยันการรับเรื่อง: ' . $case_id;
            $mail->Body    = '
            <!DOCTYPE html>
            <html lang="th">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ยืนยันการรับเรื่อง</title>
            <style>
                body {
                    font-family: "Sarabun", "Segoe UI", Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px 20px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .header .school-name {
                    font-size: 14px;
                    opacity: 0.9;
                    margin-top: 5px;
                }
                .content {
                    padding: 30px 25px;
                }
                .success-badge {
                    background: #28a745;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-size: 14px;
                    font-weight: 600;
                    display: inline-block;
                    margin-bottom: 20px;
                }
                .case-id {
                    background: #e3f2fd;
                    border-left: 4px solid #2196f3;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 0 8px 8px 0;
                }
                .case-id strong {
                    color: #1976d2;
                    font-size: 18px;
                }
                .details-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                    background: white;
                }
                .details-table tr {
                    border-bottom: 1px solid #e0e0e0;
                }
                .details-table tr:last-child {
                    border-bottom: none;
                }
                .details-table td {
                    padding: 12px 15px;
                    vertical-align: top;
                }
                .details-table .label {
                    background: #f8f9fa;
                    font-weight: 600;
                    color: #495057;
                    width: 150px;
                    border-right: 2px solid #dee2e6;
                }
                .details-table .value {
                    color: #212529;
                }
                .status-success {
                    color: #28a745;
                    font-weight: 600;
                    padding: 4px 8px;
                    background: #d4edda;
                    border-radius: 4px;
                }
                .next-steps {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 25px 0;
                }
                .next-steps h3 {
                    margin-top: 0;
                    color: #856404;
                    font-size: 16px;
                }
                .next-steps ul {
                    margin: 10px 0;
                    padding-left: 20px;
                }
                .next-steps li {
                    margin-bottom: 8px;
                    color: #856404;
                }
                .footer {
                    background: #454545ff;
                    padding: 20px;
                    text-align: center;
                    border-top: 1px solid #dee2e6;
                    color: #ffffffff;
                    font-size: 14px;
                }
                .footer .contact-info {
                    margin-top: 15px;
                    font-size: 13px;
                }
                .divider {
                    height: 2px;
                    background: linear-gradient(90deg, #667eea, #764ba2);
                    margin: 20px 0;
                    border-radius: 2px;
                }
                @media only screen and (max-width: 600px) {
                    .container {
                        margin: 10px;
                        border-radius: 5px;
                    }
                    .content {
                        padding: 20px 15px;
                    }
                    .details-table .label {
                        width: 120px;
                        font-size: 14px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>ระบบแจ้งซ่อม</h1>
                    <div class="school-name">โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</div>
                </div>
                
                <div class="content">
                    <div class="success-badge">รับเรื่องสำเร็จ</div>
                    
                    <p style="font-size: 16px; margin-bottom: 10px;"><strong>เรียน 44358</strong></p>
                    <p style="margin-bottom: 20px;">ระบบได้รับเรื่องแจ้งซ่อมของท่านเรียบร้อยแล้ว ข้อมูลรายละเอียดมีดังนี้:</p>
                    
                    <div class="case-id">
                        <strong>หมายเลขเคส: ' . htmlspecialchars($case_id) . '</strong>
                    </div>
                    
                    <table class="details-table">
                        <tr>
                            <td class="label">สถานที่</td>
                            <td class="value">' . htmlspecialchars($place) . ' ชั้น ' . htmlspecialchars($floor) . ' ห้อง ' . htmlspecialchars($room) . '</td>
                        </tr>
                        <tr>
                            <td class="label">ประเภทปัญหา</td>
                            <td class="value">' . htmlspecialchars($problem_type) . '</td>
                        </tr>
                        <tr>
                            <td class="label">รายละเอียด</td>
                            <td class="value">' . nl2br(htmlspecialchars($description)) . '</td>
                        </tr>
                        <tr>
                            <td class="label">สถานะ</td>
                            <td class="value"><span class="status-success">รับเรื่องแล้ว</span></td>
                        </tr>
                        <tr>
                            <td class="label">วันที่รับเรื่อง</td>
                            <td class="value">' . date('d/m/Y ') . '</td>
                        </tr>
                    </table>
                    
                    <div class="divider"></div>
                    
                    <div class="next-steps">
                        <h3>ขั้นตอนต่อไป</h3>
                        <ul>
                            <li>เจ้าหน้าที่จะตรวจสอบและประเมินปัญหาภายใน 24 ชั่วโมง</li>
                            <li>ท่านจะได้รับการแจ้งความคืบหน้าผ่านอีเมลนี้</li>
                            <li>หากมีความเร่งด่วน กรุณาติดต่อเจ้าหน้าที่โดยตรง</li>
                        </ul>
                    </div>
                    
                    <p style="text-align: center; font-size: 16px; color: #28a745; font-weight: 600;">
                        ขอขอบคุณที่ใช้บริการระบบแจ้งซ่อม 
                    </p>
                </div>
                
                <div class="footer">
                    <p><strong>ระบบแจ้งซ่อม โรงเรียนบดินทรเดชา</strong></p>
                    <div class="contact-info">
                        <p>อีเมลนี้ส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ</p>
                        <p>หากมีปัญหาการใช้งาน กรุณาติดต่อเจ้าหน้าที่ฝ่าย IT</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';


$mail->AltBody = "เรียน ผู้ใช้งาน\n\n" .
                 "ระบบได้รับเรื่องของท่านแล้ว\n\n" .
                 "Case ID: " . $case_id . "\n" .
                 "สถานที่: " . $place . " ชั้น " . $floor . " ห้อง " . $room . "\n" .
                 "ประเภทปัญหา: " . $problem_type . "\n" .
                 "รายละเอียดเพิ่มเติม: " . $description . "\n" .
                 "สถานะ: รับเรื่องแล้ว\n\n" .
                 "เจ้าหน้าที่จะดำเนินการตรวจสอบและแจ้งความคืบหน้าให้ทราบต่อไป\n\n" .
                 "ขอขอบคุณ\n" .
                 "ระบบแจ้งซ่อม โรงเรียนบดินทรเดชา";
            $mail->send();
            $email_sent = true;
        } 
        catch (Exception $e) {
            $error_message = date('[Y-m-d H:i:s]') . " Mailer Error: " . $mail->ErrorInfo . "\n";
            file_put_contents('email_errors.log', $error_message, FILE_APPEND);
        }
    }

    if ($email_sent) {
        echo json_encode([
            'success' => true,
            'message' => "ส่งรายงานเรียบร้อยแล้ว<br>เราได้ส่งรายละเอียดการแจ้งไปที่อีเมลของคุณ",
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'message' => "ส่งรายงานเรียบร้อยแล้ว<br>แต่ไม่สามารถส่งอีเมลยืนยันได้",
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . mysqli_stmt_error($stmt)
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($connect);
exit();
?>