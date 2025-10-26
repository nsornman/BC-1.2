<?php
// session_start();
// if (!isset($_SESSION['username'])) {
//     $_SESSION['msg'] = "Please login first.";
//     header("Location: ../login_system/login.php");
// }
include_once '../login_system/server.php';
$sql = "SELECT status, COUNT(*) AS total FROM report GROUP BY status";
$result = $connect->query($sql);

$pending = 0;
$inprogress = 0;
$done = 0;


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['status'] === 'Pending') {
            $pending = $row['total'];
        } elseif ($row['status'] === 'Inprogress') {
            $inprogress = $row['total'];
        } elseif ($row['status'] === 'Done') {
            $done = $row['total'];
        }
    }
}

$connect->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../CSS/home_lab.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anuphan">
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
            <li class="home">
                <a href="home.php">Home</a>
            </li>
            <li class="report">
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
    <div id="bg-container">
    <!-- background image as an <img> so we can use transform (GPU-accelerated) -->
    <img id="bg-image" src="../assest/birdeyeview.jpg" />
        <div class="list-container">
            <div class="list-item">
                <span>รอดำเนินการ</span>
                <span class="number"><?php echo $pending; ?></span>
            </div>
            <div class="line"></div>
            <div class="list-item">
                <span>กำลังดำเนินการ</span>
                <span class="number"><?php echo $inprogress; ?></span>
            </div>
            <div class="line"></div>
            <div class="list-item">
                <span>ดำเนินการเสร็จสิ้น</span>
                <span class="number"><?php echo $done; ?></span>
            </div>
        </div>
    </div>

    <div class="zoom-controls">
        <button id="zoom-in">+</button>
        <button id="zoom-out">-</button>
        <button id="zoom-reset">Reset</button>
    </div>

    <script>
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
            // optional: keep panning if pointer is down; here we stop
            panning = false;
            bgContainer.style.cursor = 'grab';
        });

        // wheel zoom toward cursor (use passive:false so preventDefault works)
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
</body>

</html>