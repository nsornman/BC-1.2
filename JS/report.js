const fileInput = document.getElementById("file");
const fileList = document.getElementById("file-list");

function trimFileName(name, length = 15) {
    return name.length > length ? name.substring(0, length) + "..." : name;
}

function adjustCancelButtonSpacing(fileNameElement, cancelButton) {
    const fileNameLength = fileNameElement.textContent.length;
    cancelButton.style.marginLeft = fileNameLength > 10 ? "8px" : "3px"; 
}

function saveFilesToLocalStorage() {
    const files = [];
    document.querySelectorAll(".file-item").forEach(item => {
        files.push(item.getAttribute("data-file-name"));
    });
    localStorage.setItem("uploadedFiles", JSON.stringify(files));
}

function loadFilesFromLocalStorage() {
    const savedFiles = JSON.parse(localStorage.getItem("uploadedFiles")) || [];
    savedFiles.forEach(fileName => addFileToList(fileName));
}

function addFileToList(fileName, file) {
    if (fileList.children.length >= 4) return;

    const fileItem = document.createElement("div");
    fileItem.classList.add("file-item");
    fileItem.setAttribute("data-file-name", fileName);

    const fileNameSpan = document.createElement("span");
    fileNameSpan.classList.add("file-name");
    fileNameSpan.textContent = trimFileName(fileName);
    fileNameSpan.title = fileName; // แสดงชื่อเต็มเมื่อโฮเวอร์

    const cancelButton = document.createElement("button");
    cancelButton.classList.add("cancel-btn");
    cancelButton.textContent = "✖";
    cancelButton.onclick = function () {
        fileList.removeChild(fileItem);
        saveFilesToLocalStorage(); // อัปเดต localStorage
    };

    // คลิกที่ชื่อไฟล์เพื่อแสดงภาพตัวอย่าง
    fileNameSpan.onclick = function () {
        const reader = new FileReader();
        reader.onload = function (e) {
            const image = new Image();
            image.src = e.target.result;
            image.style.maxWidth = "100%";  // กำหนดขนาดสูงสุดของตัวอย่างภาพ
            image.style.maxHeight = "100%"; // กำหนดขนาดสูงสุดของตัวอย่างภาพ
            const previewWindow = window.open("", "_blank");
            previewWindow.document.write(`<img src="${image.src}" alt="${fileName}">`);
        };
        reader.readAsDataURL(file);
    };

    fileItem.appendChild(fileNameSpan);
    fileItem.appendChild(cancelButton);
    fileList.appendChild(fileItem);

    adjustCancelButtonSpacing(fileNameSpan, cancelButton);
    saveFilesToLocalStorage();
}

fileInput.addEventListener("change", function () {
    if (fileList.children.length >= 3) {
        alert("You can only upload up to 4 images.");
        fileInput.value = "";
        return;
    }

    if (fileInput.files.length > 0) {
        Array.from(fileInput.files).forEach(file => {
            addFileToList(file.name, file); // ส่งไฟล์ไปให้ฟังก์ชัน addFileToList
        });
    }
});

// บันทึกค่า Input ทุกตัว
// function saveFormData() {
//     const inputs = document.querySelectorAll("input, textarea, select");
//     const formData = {};
//     inputs.forEach(input => {
//         if (input.type === "file") return; // ข้าม input file
//         formData[input.id || input.name] = input.value;
//     });
//     localStorage.setItem("formData", JSON.stringify(formData));

//     inputs.forEach((input, index) => {
//         if (input.type === "file") return; // ข้าม input file
    
//         const key = input.id || input.name || `input-${index}`;
    
//         if (savedData[key] !== undefined) {
//             if (input.type === "checkbox" || input.type === "radio") {
//                 input.checked = savedData[key];
//             } else {
//                 input.value = savedData[key];
//             }
//         }
//     });
// }

// function loadFormData() {
//     const savedData = JSON.parse(localStorage.getItem("formData")) || {};
//     const inputs = document.querySelectorAll("input, textarea, select");
//     inputs.forEach(input => {
//         if (input.type === "file") return; // ข้าม input file
//         if (savedData[input.id || input.name] !== undefined) {
//             input.value = savedData[input.id || input.name];
//         }
//     });
// }

// โหลดข้อมูลทั้งหมดเมื่อหน้าเว็บโหลด
// document.addEventListener("DOMContentLoaded", () => {
//     loadFilesFromLocalStorage();
//     loadFormData();
// });

// // บันทึกข้อมูลเมื่อ input เปลี่ยนแปลง
// document.addEventListener("input", saveFormData);
