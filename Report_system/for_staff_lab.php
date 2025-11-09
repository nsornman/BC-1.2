<?php 
    require_once('../login_system/server.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff</title>
    <link rel="stylesheet" href="../CSS/for_staff_lab.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anuphan">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="website icon" type="png" href="https://inf.bodin.ac.th/_resx/upload/img/brand/logo/color.png">

</head>

<body>
    <nav class="navbar">
        <div class="logo-info">
            <img class="logo" src="../asset/logo.png" alt="logo">
            <p class="school_name">Bodindecha (Sing Singhaseni) school</p>
        </div>
        <ul class="container">
            <li class = "home">
                <a href="../Report_system/home_lab.php">Home</a>
            </li>
            <li class = "report">
                <a href="../Report_system/report_lab.php">Report</a>
            </li>
            <li class="contact">
                <a href="../HTML/contact.html">Contact</a>
            </li>
        </ul>
        <div class="icon-container">
            <a href="home.html"><i class="fa-solid fa-house home" style="color: #ffffff;"></i></a>
            <a href="report.html"><i class="fa-solid fa-plus" style="color: #ffffff;"></i></a>
            <a href="contact.html"><i class="fa-solid fa-phone" style="color: #ffffff;"></i></a>
        </div>
    </nav>
    <button class="table-slide-button" onclick="toggleTable()">
        <i class="fa-solid fa-chevron-up" id="slide-icon"></i>
    </button>
    <i class="fa-solid fa-chevron-left"></i>
    <i class="fa-solid fa-chevron-right"></i>
    <div class="zoom-controls">
        <button id="zoom-in">+</button>
        <button id="zoom-out">-</button>
        <button id="zoom-reset">Reset</button>
    </div>
    <div id="bg-container">
    <img id="bg-image" src="../asset/birdeyeview.jpg"/>
    <script>
        // correctly select the bg container
        const bgContainer = document.getElementById('bg-container');
        const bgImage = document.getElementById('bg-image');
        const zoomInBtn = document.getElementById('zoom-in');
        const zoomOutBtn = document.getElementById('zoom-out');

        // state
        let scale = 1;
        let minScale = 1;
        let maxScale = Infinity;
        let panning = false;
        let start = { x: 0, y: 0 };
        let pointX = 0;
        let pointY = 0;

        // RAF batching
        let needsRender = false;
        let pendingX = 0;
        let pendingY = 0;
        let containerWidth = 0;
        let containerHeight = 0;

        const updateContainerSize = () => {
            containerWidth = bgContainer.clientWidth;
            containerHeight = bgContainer.clientHeight;
        };

        // apply transform on the image using translate3d + scale (GPU accelerated)
        const applyTransform = () => {
            // start from centered translate(-50%,-50%), then apply pan/scale
            bgImage.style.transform = `translate(-50%, -50%) translate3d(${pendingX}px, ${pendingY}px, 0) scale(${scale})`;
            pointX = pendingX;
            pointY = pendingY;
            needsRender = false;
        };

        const rafLoop = () => {
            if (needsRender) applyTransform();
            requestAnimationFrame(rafLoop);
        };
        requestAnimationFrame(rafLoop);

        // helper to extract URL from background-image
        const extractBackgroundUrl = (bg) => {
            const m = bg.match(/^url\((['"]?)(.*)\1\)$/);
            return m ? m[2] : '';
        };

        const calculateMinScale = () => {
            updateContainerSize();
            // prefer CSS background if present, otherwise use img.src
            const bg = getComputedStyle(bgContainer).backgroundImage || '';
            const url = extractBackgroundUrl(bg) || bgImage.src || '';
            if (!url) return;

            const img = new Image();
            img.src = url;
            img.onload = () => {
                // compute scale so the image covers the container (cover/fullscreen)
                const scaleX = containerWidth / img.width;
                const scaleY = containerHeight / img.height;
                minScale = Math.max(scaleX, scaleY);
                // limit maximum zoom to 500% of minScale
                maxScale = minScale * 5;
                // start with image covering the screen
                scale = minScale;
                // center image initially
                pendingX = 0;
                pendingY = 0;
                clampPending();
                needsRender = true;
            };
        };

        // clamp pendingX/pendingY so the image always covers the container
        const clampPending = () => {
            // when scaled, image size in container coords:
            const imgW = (bgImage.naturalWidth || bgImage.width) * scale;
            const imgH = (bgImage.naturalHeight || bgImage.height) * scale;

            // because the image is positioned with translate(-50%,-50%) center origin,
            // the image center is at container center + pending translation.
            // left edge = containerCenterX + pendingX - imgW/2
            // right edge = containerCenterX + pendingX + imgW/2
            const cx = containerWidth / 2;
            const cy = containerHeight / 2;

            // compute allowed min/max for pendingX so left <= 0 and right >= containerWidth
            // i.e., left <= 0  => cx + pendingX - imgW/2 <= 0  => pendingX <= -cx + imgW/2
            // and right >= containerWidth => cx + pendingX + imgW/2 >= containerWidth => pendingX >= containerWidth - cx - imgW/2
            let minX = containerWidth - cx - imgW / 2; // lower bound
            let maxX = -cx + imgW / 2; // upper bound

            // similarly for Y
            let minY = containerHeight - cy - imgH / 2;
            let maxY = -cy + imgH / 2;

            // if image is smaller than container in a dimension, center it (no panning)
            if (imgW <= containerWidth) {
                minX = maxX = 0;
            }
            if (imgH <= containerHeight) {
                minY = maxY = 0;
            }

            // clamp
            if (pendingX < minX) pendingX = minX;
            if (pendingX > maxX) pendingX = maxX;
            if (pendingY < minY) pendingY = minY;
            if (pendingY > maxY) pendingY = maxY;
        };

        // centralized zoom function: zoomAt(px, py, factor)
        // px,py are in container (viewport) coordinates where we want the image point to remain fixed
        const zoomAt = (px, py, factor) => {
            // use container center + pointX/pointY as image center
            const cx = containerWidth / 2;
            const cy = containerHeight / 2;
            // image-space coords of point relative to image center
            const xs = (px - (cx + pointX)) / scale;
            const ys = (py - (cy + pointY)) / scale;
            let newScale = scale * factor;
            // clamp newScale between minScale and maxScale
            if (newScale < minScale) newScale = minScale;
            if (newScale > maxScale) newScale = maxScale;
            if (newScale >= minScale) {
                // update scale then compute new translations so the same image-space point
                // remains under (px,py)
                scale = newScale;
                pointX = px - cx - xs * scale;
                pointY = py - cy - ys * scale;
                pendingX = pointX;
                pendingY = pointY;
                clampPending();
                needsRender = true;
            }
        };

        // zoom controls (buttons zoom at viewport center)
        zoomInBtn.addEventListener('click', () => {
            const cx = containerWidth / 2;
            const cy = containerHeight / 2;
            zoomAt(cx, cy, 1.1);
        });
        zoomOutBtn.addEventListener('click', () => {
            const cx = containerWidth / 2;
            const cy = containerHeight / 2;
            zoomAt(cx, cy, 1 / 1.1);
        });
        // reset button: restore to minScale and center
        const resetBtn = document.getElementById('zoom-reset');
        resetBtn.addEventListener('click', () => {
            updateContainerSize();
            calculateMinScale();
            // calculateMinScale will set scale = minScale and call needsRender
        });

        // pointer events: use pointer API to handle mouse+touch uniformly
        bgContainer.style.touchAction = 'none'; // prevent default touch scroll when interacting

        bgContainer.addEventListener('pointerdown', (e) => {
            e.preventDefault();
            panning = true;
            bgContainer.setPointerCapture(e.pointerId);
            start = { x: e.clientX - pointX, y: e.clientY - pointY };
            bgContainer.style.cursor = 'grabbing';
        });

        bgContainer.addEventListener('pointermove', (e) => {
            if (!panning) return;
            pendingX = e.clientX - start.x;
            pendingY = e.clientY - start.y;
            clampPending();
            needsRender = true;
        });

        const stopPan = (e) => {
            panning = false;
            bgContainer.style.cursor = 'grab';
            try { if (e && e.pointerId) bgContainer.releasePointerCapture(e.pointerId); } catch (err) {}
        };
        bgContainer.addEventListener('pointerup', stopPan);
        bgContainer.addEventListener('pointercancel', stopPan);
        bgContainer.addEventListener('pointerleave', (e) => {
            panning = false;
            bgContainer.style.cursor = 'grab';
        });

        bgContainer.addEventListener('wheel', (e) => {
            e.preventDefault();
            // pointer location relative to container
            const rect = bgContainer.getBoundingClientRect();
            const px = e.clientX - rect.left;
            const py = e.clientY - rect.top;
            const delta = -e.deltaY;
            const factor = delta > 0 ? 1.1 : 1 / 1.1;
            // use centralized zoomAt so pointer stays fixed and clamping applies
            zoomAt(px, py, factor);
        }, { passive: false });

        // init
        window.addEventListener('load', () => {
            // ensure container is positioned for absolute bg-image
            if (getComputedStyle(bgContainer).position === 'static') bgContainer.style.position = 'relative';
            bgContainer.style.cursor = 'grab';
            updateContainerSize();
            calculateMinScale();
        });
        window.addEventListener('resize', () => {
            updateContainerSize();
            calculateMinScale();
        });
    </script>
    </div>
        <div class="table-wrapper" id ="tableWrapper">
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
    <script>
        let tableVisible = false;

        function toggleTable() {
            const tableWrapper = document.getElementById('tableWrapper');
            const slideIcon = document.getElementById('slide-icon');
            
            if (tableVisible) {
                // Hide table (slide down)
                tableWrapper.classList.remove('show');
                slideIcon.className = 'fa-solid fa-chevron-up';
                tableVisible = false;
            } else {
                // Show table (slide up)
                tableWrapper.classList.add('show');
                slideIcon.className = 'fa-solid fa-chevron-down';
                tableVisible = true;
            }
        }

        // Auto-hide table when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const tableWrapper = document.getElementById('tableWrapper');
                const slideButton = document.querySelector('.table-slide-button');
                
                if (tableVisible && 
                    !tableWrapper.contains(event.target) && 
                    !slideButton.contains(event.target)) {
                    toggleTable();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const tableWrapper = document.getElementById('tableWrapper');
            const slideIcon = document.getElementById('slide-icon');
            
            if (window.innerWidth > 768) {
                // Reset to desktop view
                tableWrapper.classList.remove('show');
                slideIcon.className = 'fa-solid fa-chevron-up';
                tableVisible = false;
            }
        });

        // Status color application
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
    
</body>
</html>