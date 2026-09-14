document.addEventListener('DOMContentLoaded', function () {
    const chooseBtn   = document.getElementById('fx-choose-picture-btn');
    const fileInput   = document.getElementById('fx-picture-input');
    const preview     = document.getElementById('fx-avatar-preview');
    const statusText  = document.getElementById('fx-upload-status');

    if (!chooseBtn || !fileInput) return;

    chooseBtn.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        const file = fileInput.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            statusText.textContent = 'Only JPG, PNG or WEBP images are allowed.';
            statusText.style.color = 'var(--fx-danger)';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            statusText.textContent = 'Image must be smaller than 2MB.';
            statusText.style.color = 'var(--fx-danger)';
            return;
        }

        const formData = new FormData();
        formData.append('profile_picture', file);

        statusText.textContent = 'Uploading...';
        statusText.style.color = '';

        fetch('/FixLine/index.php?action=upload_picture', {
            method: 'POST',
            body: formData
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    preview.src = data.imageUrl + '?t=' + Date.now();
                    statusText.textContent = data.message;
                    statusText.style.color = 'var(--fx-success)';
                } else {
                    statusText.textContent = data.message || 'Upload failed. Please try again.';
                    statusText.style.color = 'var(--fx-danger)';
                }
            })
            .catch(function () {
                statusText.textContent = 'Network error. Please try again.';
                statusText.style.color = 'var(--fx-danger)';
            });
    });
});
