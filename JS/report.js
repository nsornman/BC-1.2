// const fileInput = document.getElementById("file");
// const fileList = document.getElementById("previewList");

// function trimFileName(name, length = 15) {
//     return name.length > length ? name.substring(0, length) + "..." : name;
// }

// function adjustCancelButtonSpacing(fileNameElement, cancelButton) {
//     const fileNameLength = fileNameElement.textContent.length;
//     cancelButton.style.marginLeft = fileNameLength > 10 ? "8px" : "3px"; 
// }

// function addFileToList(file, index) {
//     if (fileList.children.length >= 4) return;

//     const fileItem = document.createElement("div");
//     fileItem.classList.add("file-item");
//     fileItem.setAttribute("data-file-name", file.name);

//     const fileNameSpan = document.createElement("span");
//     fileNameSpan.classList.add("file-name");
//     fileNameSpan.textContent = trimFileName(file.name);
//     fileNameSpan.title = file.name;

//     const cancelButton = document.createElement("button");
//     cancelButton.classList.add("cancel-btn");
//     cancelButton.textContent = "✖";
//     cancelButton.onclick = function () {
//         fileList.removeChild(fileItem);
//         // Remove file from input by creating a new FileList
//         const dt = new DataTransfer();
//         Array.from(fileInput.files).forEach((f, i) => {
//             if (i !== index) dt.items.add(f);
//         });
//         fileInput.files = dt.files;
//     };

//     fileNameSpan.onclick = function () {
//         const reader = new FileReader();
//         reader.onload = function (e) {
//             const image = new Image();
//             image.src = e.target.result;
//             image.style.maxWidth = "100%";
//             image.style.maxHeight = "100%";
//             const previewWindow = window.open("", "_blank");
//             previewWindow.document(`<img src="${image.src}" alt="${file.name}">`);
//         };
//         reader.readAsDataURL(file);
//     };

//     fileItem.appendChild(fileNameSpan);
//     fileItem.appendChild(cancelButton);
//     fileList.appendChild(fileItem);

//     adjustCancelButtonSpacing(fileNameSpan, cancelButton);
// }

// fileInput.addEventListener("change", function () {
//     const files = Array.from(fileInput.files);
//     fileList.innerHTML = ""; // เคลียร์ก่อนเพื่อป้องกันซ้ำ
//     if (files.length > 4) {
//         alert("You can only upload up to 4 images.");
//         fileInput.value = "";
//         return;
//     }
//     files.forEach((file, index) => {
//         addFileToList(file, index);
//     });
// });
const fileInput = document.getElementById("file");
const fileList = document.getElementById("previewList");

function trimFileName(name, length = 15) {
    return name.length > length ? name.substring(0, length) + "..." : name;
}

function adjustCancelButtonSpacing(fileNameElement, cancelButton) {
    const fileNameLength = fileNameElement.textContent.length;
    cancelButton.style.marginLeft = fileNameLength > 10 ? "8px" : "3px"; 
}

function addFileToList(file, index) {
    if (fileList.children.length >= 4) return;

    const fileItem = document.createElement("div");
    fileItem.classList.add("file-item");
    fileItem.setAttribute("data-file-name", file.name);

    const imagePreview = document.createElement("img");
    imagePreview.classList.add("image-preview");
    imagePreview.style.maxWidth = "100px";
    imagePreview.style.maxHeight = "100px";
    imagePreview.style.marginRight = "10px";

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
    cancelButton.onclick = function () {
        fileList.removeChild(fileItem);
        const dt = new DataTransfer();
        Array.from(fileInput.files).forEach((f, i) => {
            if (i !== index) dt.items.add(f);
        });
        fileInput.files = dt.files;
    };

    fileItem.appendChild(imagePreview);
    fileItem.appendChild(fileNameSpan);
    fileItem.appendChild(cancelButton);
    fileList.appendChild(fileItem);

    adjustCancelButtonSpacing(fileNameSpan, cancelButton);
}

fileInput.addEventListener("change", function () {
    const files = Array.from(fileInput.files);
    fileList.innerHTML = "";
    if (files.length > 4) {
        alert("You can only upload up to 4 images.");
        fileInput.value = "";
        return;
    }
    files.forEach((file, index) => {
        addFileToList(file, index);
    });
});
