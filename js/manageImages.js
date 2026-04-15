import { checkAuth } from './auth.js';
import CONFIG from "./config.js";

const uploadBtn = document.getElementById('uploadImageBtn');
const refresh =  document.getElementById('refreshBtn');
const uploadForm = document.getElementById('uploadForm');
const cancel = document.getElementById('cancelUploadBtn')
const tbody = document.getElementById('imagesBody');
const uploadModal = document.getElementById('uploadModal');
const imgFile = document.getElementById('imageFile')

document.addEventListener('DOMContentLoaded', async () => {
    const user = await checkAuth();
    if (!user) {
        alert("You must be logged in.");
        window.location.href = "login.html";
        return;
    }

    // Initial load
    loadImageList();

    uploadBtn.addEventListener('click', showUploadModal);
    refresh.addEventListener('click', loadImageList);
    uploadForm.addEventListener('submit', handleImageUpload);
    cancel.addEventListener('click', closeUploadModal);
});
async function loadImageList() {
    // placeholder message.
    tbody.innerHTML = `
        <tr>
            <td>
                <em>Image Upload available.</em>
            </td>
        </tr>
        <tr>
            <td>
                <em>Image management dashboard coming soon.</em>
            </td>
        </tr>
    `;
}

function showUploadModal() {
    uploadModal.classList.remove('hidden');
    imgFile.value = '';
}

function closeUploadModal() {
    uploadModal.classList.add('hidden');
}

async function handleImageUpload(e) {
    e.preventDefault();
    const file = imgFile.files[0];
    if (!file) {
        alert("Please select an image.");
        return;
    }

    const formData = new FormData();
    formData.append('image', file);

    uploadBtn.disabled = true;
    uploadBtn.textContent = "Uploading...";

    try {
        const response = await fetch(CONFIG.API_BASE+'upload', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });

        const result = await response.json();

        if (response.ok && result.status === "success") {
            alert(`Image uploaded successfully!\n\nFilename: ${result.filename}`);
            closeUploadModal();
            loadImageList();   // refresh
        } else {
            alert(result.message || "Upload failed.");
        }
    } catch (err) {
        console.error(err);
        alert("Error uploading image. Please try again.");
    } finally {
        uploadBtn.disabled = false;
        uploadBtn.textContent = "Upload";
    }
}