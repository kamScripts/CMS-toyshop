import { checkAuth } from './auth.js';
import CONFIG from './config.js';
const addModalBtn  = document.getElementById('addProductBtn');
const addModal = document.getElementById('addProductModal');
const addProductForm = document.getElementById('addProductForm')
const addCancelBtn = document.getElementById('cancelAddBtn');
const brandIdInput = document.getElementById('productBrand');
const modelIdInput = document.getElementById('productModel');
const variantInput = document.getElementById('productVariant');
const scaleIdInput = document.getElementById('productScale');
const priceInput = document.getElementById('productPrice');
const stockInput = document.getElementById('productStock');
const addTbody = document.getElementById('productsBody');
const editModal = document.getElementById('editProductModal');
const editProductForm = document.getElementById('editProductForm');
const editCancelBtn = document.getElementById('cancelEditBtn');
const saveEditBtn = document.getElementById('saveEditBtn');

const editVariantIdInput = document.getElementById('editVariantId');
const editBrandInput = document.getElementById('editProductBrand');
const editModelInput = document.getElementById('editProductModel');
const editVariantInput = document.getElementById('editProductVariant');
const editScaleInput = document.getElementById('editProductScale');
const editPriceInput = document.getElementById('editProductPrice');
const editStockInput = document.getElementById('editProductStock');
const editSkuInput = document.getElementById('editProductSku');
const editImagepathInput = document.getElementById('editProductImagepath');


let allProducts = [];
//brandIdInput productModel
async function loadLookupData(brandSelect, scaleSelect, modelSelect) {
    try {
        // Load Brands
        const brandRes = await fetch(   CONFIG.API_BASE+'carModels/brand');
        const brandData = await brandRes.json();
                brandData.data.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.brand_id;
            opt.textContent = b.brand_name;
            brandSelect.appendChild(opt);
        });
        // Load Scales
        const scaleRes = await fetch(CONFIG.API_BASE+'carModels/scale');
        const scaleData = await scaleRes.json();
        scaleData.data.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.scale_id;
            opt.textContent = s.scale_name;
            scaleSelect.appendChild(opt);
        });

        const models = await fetch(CONFIG.API_BASE+'carModels/model');
        const modelData = await models.json();
        modelData.data.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.model_id;
            opt.textContent = s.model_name;
            modelSelect.appendChild(opt);
        });

    } catch (e) {
        console.error("Failed to load lookup data", e);
    }
}

async function loadAllProducts() {
    try {
        const res = await fetch(CONFIG.API_BASE+'carModels');
        const data = await res.json();
        allProducts = data.products || [];
        renderProducts(allProducts, addTbody);
    } catch (e) {
        console.error(e);
    }
}

function renderProducts(products, tbody) {

    tbody.innerHTML = '';

    products.forEach(p => {
        const row = document.createElement('tr');

        const fields = [
            p.variant_id,
            p.brand_name || '-',
            p.model_name,
            p.variant || '-',
            p.scale_name || '-',
            `$${parseFloat(p.price || 0).toFixed(2)}`,
            p.stock || 0
        ];

        fields.forEach(value => {
            const td = document.createElement('td');
            td.textContent = value;
            td.dataset.value = value;
            row.appendChild(td);
        });

        const actionsTd = document.createElement('td');

        const editBtn = document.createElement('button');
        editBtn.className = 'editBtn';
        editBtn.dataset.id = p.variant_id;
        editBtn.textContent = 'Edit';

        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'deleteBtn';
        deleteBtn.dataset.id = p.variant_id;
        deleteBtn.textContent = 'Delete';

        actionsTd.appendChild(editBtn);
        actionsTd.appendChild(deleteBtn);
        actionsTd.classList.add('tdBtns');
        row.appendChild(actionsTd);
        tbody.appendChild(row);
    });
    addActionListeners();
}
function addActionListeners() {
    // Delete buttons
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Delete this product?')) return;

            const id = btn.dataset.id;
            try {
                const res = await fetch(CONFIG.API_BASE+`carModels/variant/${id}`, {
                    method: 'DELETE',
                    credentials: 'include'
                });

                if (res.ok) {
                    alert("Product deleted successfully.");
                    loadAllProducts();
                } else {
                    alert("Failed to delete product.");
                }
            } catch (e) {
                console.error(e);
                alert("Error deleting product.");
            }
        });
    });

    // Edit buttons
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = parseInt(btn.dataset.id);
            const product = allProducts.find(p => p.variant_id == id);

            if (product) {
                showEditModal(product);

            } else {
                alert("Product data not found.");
            }
        });
    });
}
// Show Add-Product Modal
function showModal(modal, cancelButton) {
    modal.classList.remove('hidden');
    modal.removeAttribute('aria-hidden');
    addProductForm.reset();
    cancelButton.addEventListener('click', ()=> {
        modal.classList.add('hidden');
        modal.ariaHidden = 'true';
    });
}
// Show Edit Modal
function showEditModal(product) {
    editVariantIdInput.value = product.variant_id || '';

    editBrandInput.value = product.brand_id || '';
    editModelInput.value = product.model_id || '';
    editVariantInput.value = product.variant || '';
    editScaleInput.value = product.scale_id || '';
    editPriceInput.value = parseFloat(product.price || 0).toFixed(2);
    editStockInput.value = product.stock || 0;
    editSkuInput.value = product.sku || '';
    editImagepathInput.value = product.imagepath || '';

    editModal.classList.remove('hidden');
    editModal.removeAttribute('aria-hidden');
}

// Close Edit Modal
function closeEditModal() {
    editModal.classList.add('hidden');
    editModal.setAttribute('aria-hidden', 'true');
    editProductForm.reset();
}
// Authenticate user
document.addEventListener('DOMContentLoaded', async () => {
    const user = await checkAuth();
    if (!user) {
        alert("You must be logged in.");
        window.location.href = "login.html";
        return;
    }
    // Load brands and scales for dropdowns
    await loadLookupData(brandIdInput,scaleIdInput,modelIdInput);
    await loadLookupData(editBrandInput, editScaleInput, editModelInput)
    loadAllProducts();

});
// Cancel Edit Modal
editCancelBtn.addEventListener('click', closeEditModal);
// Show Add-Product Modal
addModalBtn.addEventListener('click', async ()=> {
    showModal(addModal, addCancelBtn);

});
// Handle Add-Product Form submission
addProductForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const brandId = brandIdInput.value;
    const modelId = modelIdInput.value;
    const variant = variantInput.value.trim();
    const scaleId = scaleIdInput.value;
    const price = priceInput.value;
    const stock = stockInput.value;

    if (!brandId || !modelId || !scaleId || !price) {
        alert("Please fill all required fields.");
        return;
    }

    const saveBtn = document.getElementById('saveAddBtn');
    saveBtn.disabled = true;
    saveBtn.textContent = "Adding...";

    try {
        const response = await fetch(CONFIG.API_BASE+'carModels/variant', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                brand_id: parseInt(brandId),
                model_id: modelId,
                variant: variant || null,
                scale_id: parseInt(scaleId),
                price: parseFloat(price),
                stock: parseInt(stock)
            }),
            credentials: 'include'
        });

        const result = await response.json();

        if (response.ok) {
            alert("Product added successfully!");
            addModal.classList.add('hidden');
            loadAllProducts(); // refresh list
        } else {
            alert(result.message || "Failed to add product.");
        }
    } catch (error) {
        console.error(error);
        alert("Error connecting to server.");
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = "Add Product";
    }
});
// ==================== EDIT FORM SUBMISSION ====================
editProductForm.addEventListener('submit', async (e) => {

    e.preventDefault();

    const variantId = editVariantIdInput.value;
    console.log(variantId);
    if (!variantId) {
        alert("Error: Variant ID missing.");
        return;
    }

    const modelId = editModelInput.value;
    const variant_name = editVariantInput.value.trim();
    const price = editPriceInput.value;
    const stock = editStockInput.value;
    const sku = editSkuInput.value.trim();
    const imagepath = editImagepathInput.value.trim();

    if (!modelId || !price) {
        alert("Please fill all required fields.");
        return;
    }

    saveEditBtn.disabled = true;
    saveEditBtn.textContent = "Saving...";

    try {
        const response = await fetch(CONFIG.API_BASE+`carModels/variant/${variantId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                model_id: parseInt(modelId),
                variant: variant_name,
                sku: sku || null,
                price: parseFloat(price),
                stock: parseInt(stock) || 0,
                imagepath: imagepath || null
            }),
            credentials: 'include'
        });

        const result = await response.json();

        if (response.ok) {
            alert("Product updated successfully!");
            closeEditModal();
            loadAllProducts(); // refresh the table
        } else {
            alert(result.message || "Failed to update product.");
        }
    } catch (error) {
        console.error(error);
        alert("Error connecting to server.");
    } finally {
        saveEditBtn.disabled = false;
        saveEditBtn.textContent = "Save Changes";
    }
});
