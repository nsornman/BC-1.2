<?php 
    require_once('../login_system/server.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/for_staff2.css">
</head>
<body>
    <!-- <nav class="navbar">
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
    </nav> -->
    <table>
        <thead>
            <tr>
                <th>case_id</th>
                <th>studentId</th>
                <th>report_date</th>
                <th>floor</th>
                <th>room</th>
                <th>problem_type</th>
                <th>description</th>
                <th>img</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                include('../login_system/server.php');
                $query = "SELECT case_id, student_id, report_date, place, floor, room, problem_type, description, img  FROM `report`";
                $path = mysqli_query($connect, $query);
                // print_r($path);
                // while ($row = mysqli_fetch_assoc($path)) {
                //     echo "<pre>";
                //     print_r($row);
                //     echo "</pre>";
                // }
                if (mysqli_num_rows($path) > 0) {
                    while ($row = mysqli_fetch_assoc($path)) {
            ?>          
                        <tr>
                            <td><?php echo $row['case_id']; ?></td>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['report_date']; ?></td>
                            <td><?php echo $row['place']; ?></td>
                            <td><?php echo $row['floor']; ?></td>
                            <td><?php echo $row['room']; ?></td>
                            <td><?php echo $row['problem_type']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><img src="<?php echo $row['img']; ?>" alt="Image" width="100"></td>
                        </tr>
            <?php
                    }
                }
            ?>
        </tbody>
    </table>
</body>
</html>