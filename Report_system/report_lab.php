<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anuphan">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/report2_lab.css">  
    <link rel="website icon" type="png" href="https://inf.bodin.ac.th/_resx/upload/img/brand/logo/color.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <a href="../PHP/home.php"><i class="fa-solid fa-house home" style="color: #ffffff;"></i></a>
            <a href="../Report_system/report.php"><i class="fa-solid fa-plus" style="color: #ffffff;"></i></a>
            <a href="contact.html"><i class="fa-solid fa-phone" style="color: #ffffff;"></i></a>
        </div>
    </nav>
    <form action="report_db_lab.php" class = "box-bg" id = "box-bg" method="post" enctype="multipart/form-data">
        <div class = "topic-1">
            <p class = "topic">Place</p>
            <div class = "line"></div>
            <div class = "top-info">
                <div class = "place" >สถานที่ <input type="text" name = "place" required></div>
                <div class="path2">
                    <div class = "floor" >ชั้น <input type="number" name = "floor" id = "floor" /></div>
                    <div class= "room"  >ห้อง <input type="text" name = "room" maxlength="4"/></div>
                <div class="divider">or</div>
                </div>
                <div class = "explane">อธิบายสถานที่อย่างละเอียด<textarea name="explane" id="explane"></textarea></div>
            </div>
        </div>
        <div class = "topic-2">
            <p class = "topic">Problem</p>
            <div class = "line"></div>
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
            <div class="preview-list" id="previewList"></div>
            <label class="custum-file-upload" id="uploadBox">
                <div class="icon">
                    <i class="fa-solid fa-file-import"></i>                
                </div>
                <div class="text">
                    <span>Click to upload image</span>
                </div>
                <input type="file" id="file" name = "file[]" class="file" accept="image/*" multiple>
            </label>
            
            <script src="../JS/report_lab.js"></script>
            <div class = "root">    
                <button type="submit" class="button2" id = "button2" name = "sub-inf">submit</button>
            </div>
            <script>
                document.getElementById('button2').addEventListener('click', function (e) {
                    e.preventDefault(); // Prevent normal form submission

                    const form = document.getElementById('box-bg');
                    const placeInput = form.querySelector('input[name="place"]');
                    const floorInput = form.querySelector('input[name="floor"]');
                    const problemTypeSelect = form.querySelector('select[name="problem_type"]');

                    if (!placeInput.value || !floorInput.value || !problemTypeSelect.value) {
                        Swal.fire('ข้อมูลไม่ครบถ้วน', 'กรุณากรอกข้อมูล สถานที่, ชั้น และ ประเภทปัญหา ให้ครบถ้วน', 'warning');
                        return; // Stop submission if validation fails
                    }

                    Swal.fire({
                        title: 'ยืนยันการส่งหรือไม่',
                        text: "คุณต้องการส่งรายงานนี้หรือไม่?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'ส่งเลย!',
                        cancelButtonText: 'ยกเลิก',
                        customClass: {
                            popup: 'my-swal-font'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData(form);

                            fetch('report_db_lab.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.text()) // Get response as text first for debugging
                            .then(text => {
                                console.log("Raw server response:", text); // Log raw response
                                try {
                                    const data = JSON.parse(text); // Try to parse the text as JSON
                                    if (data.success) {
                                        Swal.fire('ส่งสำเร็จ!', data.message || 'รายงานของคุณถูกส่งแล้ว', 'success')
                                        .then(() => window.location.href = '../Report_system/report_lab.php');
                                    } else {
                                        Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถส่งรายงานได้', 'error');
                                    }
                                } catch (error) {
                                    console.error("Failed to parse JSON:", error);
                                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถประมวลผลการตอบกลับจากเซิร์ฟเวอร์ได้ โปรดตรวจสอบ Console Log', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Fetch Error:', error);
                                Swal.fire('การเชื่อมต่อล้มเหลว', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                            });
                        }
                    });
                });
            </script>
        </div> 
    </form>
</body>
</html>