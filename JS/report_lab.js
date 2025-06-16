const fileInput = document.getElementById('file');
    const previewList = document.getElementById('previewList');
    const uploadBox = document.getElementById('uploadBox');

    let imageFiles = [];

    fileInput.addEventListener('change', function () {
      const newFiles = Array.from(this.files);

      if (imageFiles.length + newFiles.length > 4) {
        alert("สามารถอัปโหลดได้สูงสุด 4 รูป");
        return;
      }

      newFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
          imageFiles.push({ name: file.name, src: e.target.result });
          renderPreviews();
        };
        reader.readAsDataURL(file);
      });

      this.value = '';
    });

    function renderPreviews() {
      previewList.innerHTML = '';
      if (imageFiles.length > 0) {
        uploadBox.classList.add('hide-placeholder');
      } else {
        uploadBox.classList.remove('hide-placeholder');
      }

      imageFiles.forEach((file, index) => {
        const item = document.createElement('div');
        item.classList.add('preview-item');
        item.innerHTML = `
          <span class="remove-btn" onclick="removePreview(${index})">×</span>
          <img src="${file.src}" alt="preview">
          <div class="info">${file.name}</div>
        `;
        previewList.appendChild(item);
      });
    }

    function removePreview(index) {
      imageFiles.splice(index, 1);
      renderPreviews();
    }