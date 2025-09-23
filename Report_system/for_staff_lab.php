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
    <link rel="website icon" type="png" href="https://inf.bodin.ac.th/_resx/upload/img/brand/logo/color.png">

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
    <div class="table-wrapper">
    <table>
        <thead class="thead">
            <tr class="head">
                <th class="list">List</th>
                <th class="date">Date</th>
                <th class="status">Status</th>
            </tr>
        </thead>
    </table>
    
    <div class="tbody-wrapper">
        <table>
            <tbody>
                <?php 
                    $query = "SELECT case_id, problem_type, place, report_date, description, img, status FROM report ORDER BY report_date DESC";
                    $result = mysqli_query($connect, $query);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $infoId = "info_" . htmlspecialchars($row['case_id']);
                            $sliderId = "slider_" . htmlspecialchars($row['case_id']);
                            
                            $md = date("Y-m", strtotime($row['report_date']));
                            $imageHtml = '';
                            
                            if (!isset($row['img']) || empty($row['img'])) {
                                $imageHtml = '<p>ไม่มีรูปภาพ</p>';
                            } else {
                                $imageHtml = '<p class = "h-img">รูปภาพ :</p>';
                                $imageList = array_map('trim', explode(',', $row['img']));
                                
                                if (count($imageList) == 1) {
                                    // ถ้ามีรูปเดียว แสดงแบบเดิม
                                    $imageHtml .= '<img src="Report_pic/' . $md . '/'. $row['case_id'] .'/' . htmlspecialchars($imageList[0]) . '" alt="Problem Image" style="max-width: 300px; border-radius: 8px; margin: 5px;">';
                                } else {
                                    // ถ้ามีหลายรูป สร้าง slider
                                    $imageHtml .= '<div class="image-slider" id="' . $sliderId . '">';
                                    $imageHtml .= '<div class="slider-container">';
                                    $imageHtml .= '<button class="slider-btn prev-btn" onclick="slideImage(\'' . $sliderId . '\', -1)">&#8249;</button>';
                                    $imageHtml .= '<div class="slider-wrapper">';
                                    $imageHtml .= '<div class="slider-track">';
                                    
                                    foreach ($imageList as $index => $img) {
                                        $imageHtml .= '<div class="slide ' . ($index === 0 ? 'active' : '') . '">';
                                        $imageHtml .= '<img src="Report_pic/' . $md . '/'. $row['case_id'] .'/' . htmlspecialchars($img) . '" alt="Problem Image" style="max-width: 300px; border-radius: 8px;">';
                                        $imageHtml .= '</div>';
                                    }
                                    
                                    $imageHtml .= '</div>';
                                    $imageHtml .= '</div>';
                                    $imageHtml .= '<button class="slider-btn next-btn" onclick="slideImage(\'' . $sliderId . '\', 1)">&#8250;</button>';
                                    $imageHtml .= '</div>';
                                    
                                    // เพิ่ม dots indicator
                                    $imageHtml .= '<div class="slider-dots">';
                                    foreach ($imageList as $index => $img) {
                                        $imageHtml .= '<span class="dot ' . ($index === 0 ? 'active' : '') . '" onclick="currentSlide(\'' . $sliderId . '\', ' . ($index + 1) . ')"></span>';
                                    }
                                    $imageHtml .= '</div>';
                                    $imageHtml .= '</div>';
                                }
                            }
            
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['place']) . "</td>";
                            echo "<td>" . substr($row['report_date'], 0, 10) . "</td>";
                            echo "<td class = 'status'>" . htmlspecialchars($row['status']) . '
                                <div class="case-info">
                                    <button popovertarget = "' . $infoId . '" class = "icon-button" id = "icon-button"> 
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="width: 1.5rem; height: 1.5rem; vertical-align: middle; margin-left: 5px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                        </svg>
                                    </button>
                                    
                                    <div id="' . $infoId . '" class="case-detail" popover>
                                        <div class = "header">
                                            <div class = "case-title">
                                                <div class = "place">' . htmlspecialchars($row['place']) . '</div>
                                                <div class = "report_date">' . substr($row['report_date'], 0, 10) . '</div>
                                            </div>
                                            <div>' . htmlspecialchars($row['status']) . '</div>
                                        </div>
                                        <div class = "line"></div>
                                        <div class = "case-detail-content">
                                            <div class = "case-id">รหัสเคส : '. htmlspecialchars($row['case_id']) . '</div>
                                            <div class = "problem-type">ประเภทปัญหา : ' . htmlspecialchars($row['problem_type']) . '</div>
                                            <div class = "description">รายละเอียดปัญหา : '. htmlspecialchars($row['description']) . '</div>
                                            <div class = "img">' . $imageHtml . '</div>
                                        </div>
                                    </div>
                                </div>
                            </td>';
                            echo "</tr>";
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>


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

        function slideImage(sliderId, direction) {
            const slider = document.getElementById(sliderId);
            if (!slider) return;
            
            const track = slider.querySelector('.slider-track');
            const slides = slider.querySelectorAll('.slide');
            const dots = slider.querySelectorAll('.dot');
            
            if (!track || slides.length === 0) return;
            
            
            let currentIndex = 0;
            slides.forEach((slide, index) => {
                if (slide.classList.contains('active')) {
                    currentIndex = index;
                }
            });
            
            
            let newIndex = currentIndex + direction;
            
            
            if (newIndex >= slides.length) {
                newIndex = 0;
            } else if (newIndex < 0) {
                newIndex = slides.length - 1;
            }
            
            
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            
            if (slides[newIndex]) {
                slides[newIndex].classList.add('active');
            }
            if (dots[newIndex]) {
                dots[newIndex].classList.add('active');
            }
            
            
            const translateX = -newIndex * 100;
            track.style.transform = `translateX(${translateX}%)`;
        }

        function currentSlide(sliderId, slideNumber) {
            const slider = document.getElementById(sliderId);
            if (!slider) return;
            
            const track = slider.querySelector('.slider-track');
            const slides = slider.querySelectorAll('.slide');
            const dots = slider.querySelectorAll('.dot');
            
            if (!track || slides.length === 0) return;
            
            const newIndex = slideNumber - 1;
            
            
            if (newIndex < 0 || newIndex >= slides.length) return;
            
           
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
        
            if (slides[newIndex]) {
                slides[newIndex].classList.add('active');
            }
            if (dots[newIndex]) {
                dots[newIndex].classList.add('active');
            }
            
            
            const translateX = -newIndex * 100;
            track.style.transform = `translateX(${translateX}%)`;
        }

        // <- ,->
        document.addEventListener('keydown', function(e) {
            const activePopover = document.querySelector('[popover]:popover-open');
            if (activePopover) {
                const slider = activePopover.querySelector('.image-slider');
                if (slider) {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        slideImage(slider.id, -1);
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        slideImage(slider.id, 1);
                    }
                }
            }
        });

        
        document.addEventListener('DOMContentLoaded', function() {
            const sliders = document.querySelectorAll('.image-slider');
            
            sliders.forEach(slider => {
                let startX = 0;
                let isDragging = false;
                
                const sliderWrapper = slider.querySelector('.slider-wrapper');
                if (!sliderWrapper) return;
                
                
                sliderWrapper.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                    isDragging = true;
                }, { passive: true });
                
                sliderWrapper.addEventListener('touchmove', (e) => {
                    if (!isDragging) return;
                    e.preventDefault();
                }, { passive: false });
                
                sliderWrapper.addEventListener('touchend', (e) => {
                    if (!isDragging) return;
                    
                    const endX = e.changedTouches[0].clientX;
                    const diffX = startX - endX;
                    
                    
                    if (Math.abs(diffX) > 50) {
                        if (diffX > 0) {
                            slideImage(slider.id, 1);
                        } else {
                            slideImage(slider.id, -1);
                        }
                    }
                    
                    isDragging = false;
                }, { passive: true });
                
                
                sliderWrapper.addEventListener('mousedown', (e) => {
                    startX = e.clientX;
                    isDragging = true;
                    e.preventDefault();
                });
                
                sliderWrapper.addEventListener('mousemove', (e) => {
                    if (!isDragging) return;
                    e.preventDefault();
                });
                
                sliderWrapper.addEventListener('mouseup', (e) => {
                    if (!isDragging) return;
                    
                    const endX = e.clientX;
                    const diffX = startX - endX;
                    
                    if (Math.abs(diffX) > 50) {
                        if (diffX > 0) {
                            slideImage(slider.id, 1);
                        } else {
                            slideImage(slider.id, -1);
                        }
                    }
                    
                    isDragging = false;
                });
                
                
                sliderWrapper.addEventListener('dragstart', (e) => {
                    e.preventDefault();
                });
                
                
                sliderWrapper.addEventListener('mouseleave', () => {
                    isDragging = false;
                });
            });
        });
    </script>
</body>
</html>