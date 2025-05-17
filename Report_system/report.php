<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anuphan">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/report2.css">  
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
                <a href="contact.html">Contact</a>
            </li>
        </ul>
        <div class = "icon-container">
            <a href="home.html"><i class="fa-solid fa-house home" style="color: #ffffff;"></i></a>
            <a href="report.html"><i class="fa-solid fa-plus" style="color: #ffffff;"></i></a>
            <a href="contact.html"><i class="fa-solid fa-phone" style="color: #ffffff;"></i></a>
        </div>
    </nav>
    <form action="report_db.php" class = "box-bg" id = "box-bg" method="post">
        <button id="toggleButton" class = "toggle-Button"></button>
        <div class = "top">
            <p class = "topic">Place</p>
            <div class = "line-1"></div>
            <div class = "top-info">
                <div class = "place" >สถานที่ <input type="text" name = "place" required></div>
                <div class="path2">
                    <div class = "floor" >ชั้น <input type="number" name = "floor"></div>
                    <div class= "room" >ห้อง <input type="number" name = "room"></div>
                </div>   
            </div>
        </div>
        <div class = "middle">
            <p class = "topic">Problem</p>
            <div class = "line-2"></div>
            <div class="type">ประเภท
                <select class = "choose" name="problem_type" required>
                    <option value="" disabled selected hidden>กรุณาเลือกประเภทปัญหา</option>
                    <option value="อุปกรณ์เครื่องใช้ในห้อง หมด/มีปัญหา">อุปกรณ์เครื่องใช้ในห้อง หมด/มีปัญหา</option>
                    <option value="ห้องน้ำ ชำรุด/ไม่สะอาด/ทิชชู่หมด">ห้องน้ำ ชำรุด/ไม่สะอาด/ทิชชู่หมด</option>
                    <option value="ทางเดินชำรุดภายใน/ภายนอกอาคารเสียหาย">ทางเดินภายใน/ภายนอกอาคารชำรุดเสียหาย</option>
                    <option value="ถังขยะล้น">ถังขยะล้น</option>
                    <option value="ตู้กดน้ำกดไม่ออก">ตู้กดน้ำกดไม่ออก</option>
                    <option value="อื่นๆ">อื่นๆ</option>
                </select>
            </div>
            <div class = "to-info">
                <div class = "more-info">คำอธิบาย</div>
                <textarea id="autoExpand" placeholder="กรุณาอธิบายเพิ่มเติม . . ." name = "description"></textarea> 
            </div>             
            <input type="file" id="file" name = "img" class="file" accept="image/*" multiple >
            <label for="file" class="custom-file-upload">Upload image( Max 4 Img )</label>
            <div class="file-list" id="file-list"></div>
            <script src="../JS/report.js"></script>
            <script src="../JS/slide.js"></script>
        </div>
        <div class = "root">    
            <button type="submit" class="button2" name = "sub-inf">submit</button>
        </div>  
    </form>
</body>
</html>