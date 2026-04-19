import { checkAuth } from "./auth.js";
import CONFIG from "./config.js";
const selectForm = document.getElementById('category');
const categoryType = document.getElementById('categoryType');
const categoryName = document.getElementById('categoryName');
const modelName = document.getElementById('modelName');
const modelBrand = document.getElementById('modelBrand');
const modelScale = document.getElementById('modelScale');
const modelCollection = document.getElementById('modelCollection');
const modelDesc = document.getElementById('modelDescription');
const smallForm = document.getElementById('simpleNameGroup');
const largeForm = document.getElementById('modelFields');
const newRecordBtn = document.getElementById('addCategoryBtn');
const modalForm = document.getElementById('addCategoryForm');
const cancelModalBtn = document.getElementById('cancelModalBtn');
const catBody = document.getElementById('categoriesBody');
const modal = document.getElementById('addCategoryModal');
let currentType = '';


document.addEventListener('DOMContentLoaded', async () => {
    const user = await checkAuth();
    if (!user) {
        alert("You must be logged in.");
        window.location.href = "login.html";
        return;
    }

    // Initial table
    loadAllCategories('brand');

    // Add New Item button
    newRecordBtn.addEventListener('click', showAddModal);

    // Form submit
    modalForm.addEventListener('submit', handleAddSubmit);

    // Cancel button in modal
    cancelModalBtn.addEventListener('click', closeAddModal);

    // Category selector (top dropdown)
    selectForm.addEventListener('change', (e) => {
        const value = e.target.value.trim().toLowerCase();
        if (value) {
            currentType = value;
            loadAllCategories(value);
        }
    });
});
// Fetch content
async function loadAllCategories(type) {
    currentType = type || 'brand';
    try {
        const res = await fetch(CONFIG.API_BASE + `carModels/${currentType}`);
        const data = await res.json();
        if (data.status === "success") {
            renderCategories(data.data, currentType);
        } else {
            console.error("API Error:", data);
        }
    } catch (e) {
        console.error("Load categories error:", e);
    }
}
// Render content
function renderCategories(items, type) {
    const tbody = catBody;
    tbody.innerHTML = '';
    items.forEach(item => {
        const idField = Object.keys(item)[0];
        const nameField = Object.keys(item)[1];
        //Create and populate table
        const row = document.createElement('tr');

        // ID cell
        const tdId = document.createElement('td');
        tdId.textContent = item[idField];

        // Type cell
        const tdType = document.createElement('td');
        tdType.textContent = type;

        // Name cell
        const tdName = document.createElement('td');
        tdName.textContent = item[nameField] ;

        // Actions cell
        const tdActions = document.createElement('td');
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'deleteBtn';
        deleteBtn.dataset.id = item[idField];
        deleteBtn.dataset.type = type;
        deleteBtn.textContent = 'Delete';

        tdActions.appendChild(deleteBtn);
        row.append(tdId,tdType,tdName,tdActions);
        tbody.appendChild(row);
    });
    addDeleteListeners();
}
// Modal
function showAddModal() {
    modalForm.reset();
    modal.classList.remove('hidden');
    // Set the modal type select to match current table
    categoryType.value = currentType;
    toggleModelFields();
}
function closeAddModal() {
    modal.classList.add('hidden');
}
// Modal contains two forms one for 2-column tables and second for model table
function toggleModelFields() {
    const type = categoryType.value;
    const simpleGroup = smallForm;
    const modelGroup = largeForm;
    if (type === 'model') {
        simpleGroup.classList.add('hidden');
        modelGroup.classList.remove('hidden');
        loadModelDropdowns();
    } else {
        simpleGroup.classList.remove('hidden');
        modelGroup.classList.add('hidden');
    }
}
// Populate model form select options.
async function loadModelDropdowns() {
    try {
        const [brandRes, scaleRes, collRes] = await Promise.all([
            fetch(CONFIG.API_BASE + 'carModels/brand'),
            fetch(CONFIG.API_BASE + 'carModels/scale'),
            fetch(CONFIG.API_BASE + 'carModels/collection')
        ]);
        const brandData = await brandRes.json();
        const scaleData = await scaleRes.json();
        const collData = await collRes.json();
        populateSelect('modelBrand', brandData.data, 'brand_id', 'brand_name');
        populateSelect('modelScale', scaleData.data, 'scale_id', 'scale_name');
        populateSelect('modelCollection', collData.data, 'collection_id', 'category_name');
    } catch (e) {
        console.error("Failed to load dropdowns", e);
    }
}
function populateSelect(selectId, items, idField, nameField) {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="">-- Select --</option>';
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item[idField];
        opt.textContent = item[nameField];
        select.appendChild(opt);
    });
}

// add-form submission

async function handleAddSubmit(e) {
    e.preventDefault();

    const type = categoryType.value;
    let bodyData = {};
    //Data to create Model record
    if (type === 'model') {
        bodyData = {
            model_name: modelName.value.trim(),
            brand_id: parseInt(modelBrand.value) || null,
            scale_id: parseInt(modelScale.value) || null,
            collection_id: modelCollection.value ?
                parseInt(modelCollection.value) : null,
            description: modelDesc.value.trim() || null
        };
    } else { //Data to create any small 2 column table ({table}_id, name)
        bodyData = {
            [`${type}_name`]: categoryName.value.trim()
        };
    }
    if (Object.values(bodyData).every(v => !v)) {
        alert("Please fill the required fields.");
        return;
    }
    const saveBtn = document.getElementById('saveAddBtn');
    saveBtn.disabled = true;
    saveBtn.textContent = "Adding...";
    try {
        const response = await fetch(CONFIG.API_BASE+`carModels/${type}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bodyData),
            credentials: 'include'
        });
        const result = await response.json();
        if (response.ok) {
            alert(`${type} added successfully!`);
            closeAddModal();
            loadAllCategories(currentType);   // refresh current table
        } else {
            alert(result.message || "Failed to add.");
        }
    } catch (err) {
        alert("Error connecting to server.");
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = "Add";
    }
}

// Delete record
function addDeleteListeners() {
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Delete this item?')) return;
            const id = btn.dataset.id;
            const type = btn.dataset.type;
            try {
                const res = await fetch(CONFIG.API_BASE+`carModels/${type}/${id}`, {
                    method: 'DELETE',
                    credentials: 'include'
                });
                if (res.ok) {
                    alert("Deleted successfully.");
                    loadAllCategories(currentType);
                } else {
                    alert("Failed to delete.");
                }
            } catch (e) {
                alert("Error while deleting");
            }
        });
    });
}
