// const fileInput = document.getElementById("file");
// const fileList = document.getElementById("previewList");
// const icon = document.querySelector("#uploadBox .icon");
// const text = document.querySelector("#uploadBox .text");

// function trimFileName(name, length = 15) {
//     return name.length > length ? name.substring(0, length) + "..." : name;
// }

// function updateIconTextVisibility() {
//     const hasFiles = fileList.children.length > 0;
//     icon.style.display = hasFiles ? "none" : "";
//     text.style.display = hasFiles ? "none" : "";
// }

// function addFileToList(files) {
//     fileList.innerHTML = "";
//     files.forEach((file, index) => {
//         const fileItem = document.createElement("div");
//         fileItem.classList.add("file-item");
//         fileItem.setAttribute("data-file-name", file.name);

//         const imagePreview = document.createElement("img");
//         imagePreview.classList.add("image-preview");
//         imagePreview.style.maxWidth = "50%";
//         imagePreview.style.maxHeight = "50%";
        

//         const reader = new FileReader();
//         reader.onload = function (e) {
//             imagePreview.src = e.target.result;
//         };
//         reader.readAsDataURL(file);

//         const fileNameSpan = document.createElement("span");
//         fileNameSpan.classList.add("file-name");
//         fileNameSpan.textContent = trimFileName(file.name);
//         fileNameSpan.title = file.name;

//         const cancelButton = document.createElement("button");
//         cancelButton.classList.add("cancel-btn");
//         cancelButton.textContent = "✖";
//         cancelButton.onclick = function () {
//             const dt = new DataTransfer();
//             const newFiles = Array.from(fileInput.files).filter((f, i) => i !== index);
//             newFiles.forEach(f => dt.items.add(f));
//             fileInput.files = dt.files;
//             addFileToList(newFiles);
//             updateIconTextVisibility();
//         };

//         fileItem.appendChild(imagePreview);
//         fileItem.appendChild(fileNameSpan);
//         fileItem.appendChild(cancelButton);
//         fileList.appendChild(fileItem);
//     });
//     updateIconTextVisibility();
// }

// fileInput.addEventListener("change", function () {
//     const files = Array.from(fileInput.files);
//     if (files.length > 4) {
//         alert("You can only upload up to 4 images.");
//         fileInput.value = "";
//         return;
//     }
//     addFileToList(files);
// });
const fileInput = document.getElementById("file");
const fileList = document.getElementById("previewList");
const icon = document.querySelector(".icon");
const text = document.querySelector(".text");

let currentFiles = [];

function trimFileName(name, length = 15) {
    return name.length > length ? name.substring(0, length) + "..." : name;
}

function updateFileInput() {
    const dt = new DataTransfer();
    currentFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function addFileToList(files) {
    if (files.length > 0) {
        if (icon) icon.style.display = "none";
        if (text) text.style.display = "none";
    } else {
        if (icon) icon.style.display = "flex";
        if (text) text.style.display = "flex";
    }

    fileList.innerHTML = "";
    files.forEach((file, index) => {
        const fileItem = document.createElement("div");
        fileItem.classList.add("file-item");
        fileItem.setAttribute("data-file-name", file.name);

        const imagePreview = document.createElement("img");
        imagePreview.style.width = "100%";
        imagePreview.style.height = "50%";
        imagePreview.style.objectFit = "cover";

        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        const fileNameSpan = document.createElement("span");
        fileNameSpan.classList.add("file-name");
        fileNameSpan.textContent = trimFileName(file.name);
        fileNameSpan.title = file.name;

        const cancelButton = document.createElement("button");
        cancelButton.classList.add("cancel-btn");
        cancelButton.textContent = "✖";
        cancelButton.onclick = function (e) {
            e.stopPropagation(); // ป้องกันการไปกระตุ้น input
            currentFiles.splice(index, 1);
            updateFileInput();
            addFileToList(currentFiles);
        };

        fileItem.appendChild(imagePreview);
        fileItem.appendChild(fileNameSpan);
        fileItem.appendChild(cancelButton);
        fileList.appendChild(fileItem);
    });
}

fileInput.addEventListener("click", function (e) {
    e.target.value = null; // รีเซ็ต input เพื่อให้เลือกไฟล์ซ้ำได้
});

fileInput.addEventListener("change", function () {
    const selectedFiles = Array.from(fileInput.files);
    if (currentFiles.length + selectedFiles.length > 4) {
        alert("You can only upload up to 4 images.");
        updateFileInput();
        return;
    }
    currentFiles = currentFiles.concat(selectedFiles);
    updateFileInput();
    addFileToList(currentFiles);
});
