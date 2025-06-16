<?php 
    require_once('../login_system/server.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/for_staff_lab.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anuphan">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>

<body>
    <nav class="navbar">
        <div class="logo-info">
            <img class="logo" src="../cropped-bodin.png" alt="logo">
            <p class="school_name">Bodindecha (Sing Singhaseni) school</p>
        </div>
        <ul class="container">
            <li class = "home">
                <a href="../PHP/home.php">Home</a>
            </li>
            <li class = "report">
                <a href="../Report_system/report.php">Report</a>
            </li>
            <li class="contact">
                <a href="HTML/contact.html">Contact</a>
            </li>
        </ul>
    </nav>
    <table class = "Report-box">
        <thead class = "thead">
            <tr class = "head">
                <th class = "list">List</th>
                <th class = "date">Date</th>
                <th class = "status">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $query = "SELECT case_id, problem_type, place, report_date, description, img, status FROM report ORDER BY report_date DESC";
                $result = mysqli_query($connect, $query);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $infoId = "info_" . htmlspecialchars($row['case_id']);  
                        $imageHtml = ''; 
                        if (!isset($row['img']) || empty($row['img'])) {
                            $imageHtml = '<p>ไม่มีรูปภาพ</p>';
                        } else {
                            $imageHtml = '<p>รูปภาพ :</p>' .
                                        '<img src="' . htmlspecialchars($row['img']) . '" alt="Problem Image" style="max-width: 300px; border-radius: 8px;">';
                        }
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['place']) . "</td>";
                        echo "<td>" . substr($row['report_date'], 0, 10) . "</td>";
                        echo "<td class = 'status'>" . htmlspecialchars($row['status']) . '
                            <div class="case-info">
                                <button popovertarget = "' . $infoId . '" class = "icon-button" id = "icon-button"> <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="width: 1.5rem; height: 1.5rem; vertical-align: middle; margin-left: 5px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </button>  
                                <div id="' . $infoId . '" class="case-detail"  popover>                                                    
                                    <div class = "header">
                                        <div class = "case-title">
                                            <div class = "place">' . htmlspecialchars($row['place']) . '</div>
                                            <div class = "report_date">' . substr($row['report_date'], 0, 10) . '</div>
                                        </div>                 
                                        <div>' . htmlspecialchars($row['status']) . '</div>
                                    </div>
                                    <div class = "line"></div>
                                    <div class = "case-detail-content">
                                        <div class = "case-id">  รหัสเคส :  '. htmlspecialchars($row['case_id']) . '</div>
                                        <div class = "problem-type">ประเภทปัญหา : ' . htmlspecialchars($row['problem_type']) . '</div>
                                        <div class = "description">รายละเอียดปัญหา : '. htmlspecialchars($row['description']) . '</div>
                                        <div class = "img">' . $imageHtml . '</div>
                                    </div>  
                                </div>   
                            </div>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>ไม่มีข้อมูล</td></tr>";
                }
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const statusCells = document.querySelectorAll("td.status");

                    statusCells.forEach((cell) => {
                        const statusText = cell.textContent.trim();

                        if (statusText.includes("Pending")) {
                        cell.classList.add("status-pending");
                        } else if (statusText.includes("Inprogress")) {
                        cell.classList.add("status-inprogress");
                        } else if (statusText.includes("Done")) {
                        cell.classList.add("status-done");
                        }
                    });
                });
            </script>  
        </tbody>
    </table>
</body>
</html>