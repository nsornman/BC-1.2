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
            e.stopPropagation(); 
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
    e.target.value = null; 
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

